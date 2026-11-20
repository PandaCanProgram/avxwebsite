import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/admin-messages.js', import.meta.url), 'utf8');

const element = (properties = {}) => ({
    events: {}, attributes: new Map(), hidden: false, textContent: '',
    addEventListener(name, callback) { this.events[name] = callback; },
    setAttribute(name, value) { this.attributes.set(name, value); },
    removeAttribute(name) { this.attributes.delete(name); },
    focus() { this.focused = true; },
    ...properties,
});

const setup = (initial = [1]) => {
    const message = (id, text = `Message ${id}`) => ({
        dataset: { chatMessage: String(id) }, querySelector: () => ({ textContent: text }),
    });
    const log = {
        children: initial.map((id) => message(id)),
        querySelectorAll() { return this.children; },
        insertBefore(node, next) {
            const index = next ? this.children.indexOf(next) : this.children.length;
            this.children.splice(index, 0, node);
        },
        get lastElementChild() { return this.children.at(-1); },
    };
    const body = element({ value: 'Hello team' });
    const clientId = { value: 'original-request-id' };
    const stream = { scrollTop: 0, scrollHeight: 1000, clientHeight: 400 };
    const form = element({
        action: 'https://avx.test/admin/messages/1/send',
        elements: { namedItem: (name) => name === 'body' ? body : clientId },
    });
    const send = element({ disabled: false });
    const error = element({ hidden: true });
    const status = element();
    const older = element({ dataset: { before: String(initial[0] || 0) } });
    const empty = element({ hidden: initial.length > 0 });
    const preview = element();
    const fields = new Map([
        ['[data-chat-stream]', stream], ['[data-chat-log]', log], ['[data-chat-empty]', empty],
        ['[data-chat-older]', older], ['[data-chat-composer]', form], ['[data-chat-send]', send],
        ['[data-chat-error]', error], ['[data-chat-status]', status],
    ]);
    form.querySelector = (selector) => fields.get(selector);
    const room = {
        dataset: { feedUrl: 'https://avx.test/admin/messages/1/feed', cursor: String(initial.at(-1) || 0), groupId: '1' },
        querySelector: (selector) => fields.get(selector),
    };
    const document = element({
        hidden: false,
        querySelector: (selector) => selector === '[data-chat-room]' ? room : preview,
        querySelectorAll: () => [],
        createElement: () => {
            let nodes = [];
            return {
                set innerHTML(value) { nodes = JSON.parse(value).map(({ id, text }) => message(id, text)); },
                content: { querySelectorAll: () => nodes },
            };
        },
    });
    const timers = new Map();
    const requests = [];
    let responder;
    let sequence = 0;
    runInNewContext(source, {
        document, URL,
        window: { fetch: true, location: { href: 'http://avx.test/admin/messages/1' } },
        AbortSignal: { timeout: () => ({}) },
        FormData: class { constructor() { this.body = body.value; this.clientId = clientId.value; } },
        fetch: async (url, options) => {
            requests.push({ url: String(url), options });
            return responder(url, options);
        },
        setTimeout: (callback, delay) => { timers.set(++sequence, { callback, delay }); return sequence; },
        clearTimeout: (id) => timers.delete(id),
    });
    return {
        body, clientId, stream, log, error, status, send, older, empty, document, requests, timers,
        respond: (data, code = 200) => { responder = async () => ({ ok: code < 400, status: code, json: async () => ({ next_client_id: 'new-server-id', ...data }) }); },
        fail: () => { responder = async () => { throw new TypeError('offline'); }; },
        submit: () => form.events.submit({ preventDefault() {} }),
        poll: async () => {
            const [id, scheduled] = [...timers].at(-1);
            timers.delete(id);
            await scheduled.callback();
        },
        ids: () => log.children.map((node) => Number(node.dataset.chatMessage)),
    };
};

const html = (...ids) => JSON.stringify(ids.map((id) => ({ id, text: `Message ${id}` })));

test('sending appends once, clears the acknowledged draft, and rotates the request ID', async () => {
    const ui = setup([]);
    ui.respond({ html: html(1), id: 1, body: 'Hello team' });
    await ui.submit();
    assert.deepEqual(ui.ids(), [1]);
    assert.equal(ui.body.value, '');
    assert.notEqual(ui.clientId.value, 'original-request-id');
    assert.equal(ui.empty.hidden, true);
    assert.equal(ui.send.disabled, false);
});

test('an interrupted send preserves the draft and request ID for a safe retry', async () => {
    const ui = setup();
    ui.fail();
    await ui.submit();
    assert.equal(ui.body.value, 'Hello team');
    assert.equal(ui.clientId.value, 'original-request-id');
    assert.equal(ui.error.hidden, false);
    assert.match(ui.error.textContent, /draft is kept/);
    assert.equal(ui.send.disabled, false);
    ui.respond({ html: html(2), id: 2, body: 'Hello team' });
    await ui.submit();
    assert.equal(ui.requests[1].options.body.clientId, 'original-request-id');
    assert.deepEqual(ui.ids(), [1, 2]);
});

test('blank messages stay in the composer and do not reach the server', async () => {
    const ui = setup();
    ui.body.value = '  ';
    await ui.submit();
    assert.equal(ui.requests.length, 0);
    assert.equal(ui.body.focused, true);
    assert.equal(ui.body.attributes.get('aria-invalid'), 'true');
});

test('a retried message acknowledged with old content does not erase an edited draft', async () => {
    const ui = setup();
    ui.body.value = 'An updated draft';
    ui.respond({ html: html(2), id: 2, body: 'The previous draft' });
    await ui.submit();
    assert.equal(ui.body.value, 'An updated draft');
    assert.match(ui.status.textContent, /updated draft is ready/);
    assert.notEqual(ui.clientId.value, 'original-request-id');
});

test('sending does not skip messages that arrive before its acknowledgement', async () => {
    const ui = setup([1]);
    ui.respond({ html: html(3), id: 3, body: 'Hello team' });
    await ui.submit();
    ui.respond({ html: html(2, 3), last_id: 3, has_more: false });
    await ui.poll();
    assert.match(ui.requests[1].url, /after=1/);
    assert.deepEqual(ui.ids(), [1, 2, 3]);
    ui.respond({ html: html(), last_id: null, has_more: false });
    await ui.poll();
    assert.match(ui.requests[2].url, /after=3/);
});

test('refresh preserves reading position when the user scrolls up', async () => {
    const ui = setup();
    ui.stream.scrollTop = 100;
    ui.respond({ html: html(2), last_id: 2, has_more: false });
    await ui.poll();
    assert.equal(ui.stream.scrollTop, 100);
    assert.deepEqual(ui.ids(), [1, 2]);
});

test('loading earlier messages prepends them in order and keeps the live cursor', async () => {
    const ui = setup([51, 52]);
    ui.respond({ html: html(49, 50), first_id: 49, last_id: 50, has_more: false });
    await ui.older.events.click();
    assert.deepEqual(ui.ids(), [49, 50, 51, 52]);
    assert.equal(ui.older.hidden, true);
    ui.respond({ html: html(), last_id: null, has_more: false });
    await ui.poll();
    assert.match(ui.requests[1].url, /after=52/);
});

test('expired sessions stop refresh and keep an unsent draft', async () => {
    const ui = setup();
    ui.respond({}, 419);
    await ui.submit();
    assert.equal(ui.body.value, 'Hello team');
    assert.match(ui.error.textContent, /session or group access/);
    await ui.poll();
    assert.equal(ui.timers.size, 0);
    assert.equal(ui.requests.length, 1);
});

test('hidden tabs stop polling and resume when visible', async () => {
    const ui = setup();
    ui.document.hidden = true;
    ui.document.events.visibilitychange();
    assert.equal(ui.timers.size, 0);
    ui.document.hidden = false;
    ui.document.events.visibilitychange();
    assert.equal(ui.timers.size, 1);
});
