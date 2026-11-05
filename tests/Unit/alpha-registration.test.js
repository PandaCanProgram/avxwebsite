import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/alpha-registration.js', import.meta.url), 'utf8');

const setup = (fetchResponse) => {
    let submit;
    let calls = 0;
    const email = {
        name: 'alpha_email',
        value: 'visitor@example.com',
        attributes: {},
        removeAttribute(name) { delete this.attributes[name]; },
        setAttribute(name, value) { this.attributes[name] = value; },
        focus() { this.focused = true; },
    };
    const name = { ...email, name: 'alpha_name', value: 'Visitor Name', attributes: {} };
    const button = { innerHTML: 'Pre-register', disabled: false };
    const status = { dataset: {}, hidden: true, focus() { this.focused = true; } };
    const form = {
        action: 'https://avxengineering.com/alpha-registration',
        elements: { namedItem: (key) => key === 'alpha_name' ? name : email },
        querySelector: (selector) => selector.startsWith('button') ? button : status,
        reportValidity: () => true,
        attributes: {},
        setAttribute(name, value) { this.attributes[name] = value; },
        removeAttribute(name) { delete this.attributes[name]; },
        addEventListener: (event, callback) => { submit = callback; },
    };
    runInNewContext(source, {
        document: { querySelector: () => null, querySelectorAll: () => [form] },
        window: { setTimeout: () => 1, clearTimeout: () => {} },
        AbortController,
        FormData: class { constructor(target) { assert.equal(target, form); } },
        fetch: async (url, options) => {
            calls++;
            assert.equal(url, form.action);
            assert.equal(options.method, 'POST');
            assert.equal(options.headers.Accept, 'application/json');
            return fetchResponse();
        },
    });

    return { name, email, button, status, form, calls: () => calls, submit: () => submit({ preventDefault() {} }) };
};

const response = (code, body) => ({ status: code, ok: code === 200, json: async () => body });

test('successful pre-registration shows confirmation and clears name and email', async () => {
    const ui = setup(() => response(200, { message: "You're on the list!" }));
    await ui.submit();
    assert.equal(ui.email.value, '');
    assert.equal(ui.name.value, '');
    assert.equal(ui.status.textContent, "You're on the list!");
    assert.equal(ui.status.dataset.state, 'success');
    assert.equal(ui.status.focused, true);
    assert.equal(ui.button.disabled, false);
    assert.equal(ui.email.readOnly, false);
    assert.equal(ui.form.attributes['aria-busy'], undefined);
});

test('rapid double submissions create only one request and keep the email stable while sending', async () => {
    let resolve;
    const pending = new Promise((done) => { resolve = done; });
    const ui = setup(() => pending);
    const first = ui.submit();
    await ui.submit();
    assert.equal(ui.calls(), 1);
    assert.equal(ui.button.disabled, true);
    assert.equal(ui.email.readOnly, true);
    assert.equal(ui.name.readOnly, true);
    resolve(response(200, { message: 'Registered' }));
    await first;
    assert.equal(ui.button.disabled, false);
});

test('validation errors preserve the email and focus the invalid field', async () => {
    const ui = setup(() => response(422, { errors: { alpha_email: ['Enter a valid email.'] } }));
    await ui.submit();
    assert.equal(ui.email.value, 'visitor@example.com');
    assert.equal(ui.email.attributes['aria-invalid'], 'true');
    assert.equal(ui.email.focused, true);
    assert.equal(ui.status.textContent, 'Enter a valid email.');
    assert.equal(ui.status.dataset.state, 'error');
});

test('name errors focus the name field and keep both values', async () => {
    const ui = setup(() => response(422, { errors: { alpha_name: ['Please enter your name.'] } }));
    await ui.submit();
    assert.equal(ui.name.attributes['aria-invalid'], 'true');
    assert.equal(ui.name.focused, true);
    assert.equal(ui.name.value, 'Visitor Name');
    assert.equal(ui.email.value, 'visitor@example.com');
});

test('network failure preserves the email and allows retry', async () => {
    let failing = true;
    const ui = setup(() => {
        if (failing) { throw new Error('Offline'); }
        return response(200, { message: 'Registered' });
    });
    await ui.submit();
    assert.equal(ui.email.value, 'visitor@example.com');
    assert.equal(ui.name.value, 'Visitor Name');
    assert.equal(ui.status.dataset.state, 'error');
    assert.equal(ui.button.disabled, false);
    failing = false;
    await ui.submit();
    assert.equal(ui.status.dataset.state, 'success');
    assert.equal(ui.calls(), 2);
});

for (const [code, message] of [[404, 'closed'], [419, 'expired'], [429, 'wait'], [500, 'try again']]) {
    test(`HTTP ${code} explains the problem without losing the email`, async () => {
        const ui = setup(() => response(code, {}));
        await ui.submit();
        assert.equal(ui.email.value, 'visitor@example.com');
        assert.equal(ui.status.dataset.state, 'error');
        assert.equal(ui.status.textContent.includes(message), true);
        assert.equal(ui.button.disabled, false);
    });
}

const setupDialog = (restore = false) => {
    const classes = new Set();
    const styles = new Map();
    const events = {};
    let openClick;
    let closeClick;
    let resize;
    const name = { focus() { this.focused = true; } };
    const trigger = {
        addEventListener: (_, callback) => { openClick = callback; },
        focus() { this.focused = true; },
    };
    const banner = { height: 52, getBoundingClientRect() { return { height: this.height }; } };
    const dialog = {
        open: restore,
        showModal() { this.open = true; },
        close() { this.open = false; events.close(); },
        querySelector: (selector) => selector === '[data-alpha-registration-close]'
            ? { addEventListener: (_, callback) => { closeClick = callback; } }
            : selector === '[name="alpha_name"]' ? name : null,
        addEventListener: (name, callback) => { events[name] = callback; },
        hasAttribute: () => restore,
        removeAttribute() { this.open = false; },
        getBoundingClientRect: () => ({ left: 100, right: 500, top: 100, bottom: 600 }),
    };
    runInNewContext(source, {
        document: {
            querySelector: (selector) => selector === '.alpha-registration' ? banner
                : selector === '[data-alpha-registration-dialog]' ? dialog : trigger,
            querySelectorAll: () => [],
            documentElement: {
                style: { setProperty: (name, value) => styles.set(name, value) },
                classList: { add: (name) => classes.add(name), remove: (name) => classes.delete(name) },
            },
        },
        window: { addEventListener: (_, callback) => { resize = callback; } },
    });
    return { dialog, trigger, name, banner, styles, classes, events, open: () => openClick(), close: () => closeClick(), resize: () => resize() };
};

test('pre-register opens a modal, focuses name, and restores scrolling and trigger focus on close', () => {
    const ui = setupDialog();
    assert.equal(ui.dialog.open, false);
    ui.open();
    assert.equal(ui.dialog.open, true);
    assert.equal(ui.name.focused, true);
    assert.equal(ui.classes.has('alpha-registration-dialog-open'), true);
    ui.close();
    assert.equal(ui.dialog.open, false);
    assert.equal(ui.classes.has('alpha-registration-dialog-open'), false);
    assert.equal(ui.trigger.focused, true);
});

test('server validation restore opens the modal, and backdrop clicks close it without treating dialog padding as backdrop', () => {
    const ui = setupDialog(true);
    assert.equal(ui.dialog.open, true);
    ui.events.click({ target: ui.dialog, clientX: 110, clientY: 110 });
    assert.equal(ui.dialog.open, true);
    ui.events.click({ target: ui.dialog, clientX: 20, clientY: 20 });
    assert.equal(ui.dialog.open, false);
});

test('sticky scroll offsets follow the actual banner height', () => {
    const ui = setupDialog();
    assert.equal(ui.styles.get('--story-header-height'), '132px');
    ui.banner.height = 68;
    ui.resize();
    assert.equal(ui.styles.get('--story-header-height'), '148px');
});
