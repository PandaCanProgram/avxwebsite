import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/analytics.js', import.meta.url), 'utf8');

class TestElement {
    constructor(tag, attributes = {}, parent = null) {
        this.tag = tag;
        this.attributes = attributes;
        this.parent = parent;
        this.dataset = Object.fromEntries(Object.entries(attributes)
            .filter(([name]) => name.startsWith('data-'))
            .map(([name, value]) => [name.slice(5).replace(/-([a-z])/g, (_, letter) => letter.toUpperCase()), value]));
    }

    hasAttribute(name) { return Object.hasOwn(this.attributes, name); }
    getAttribute(name) { return this.attributes[name] ?? null; }
    closest() {
        if (this.tag === 'button' || (this.tag === 'a' && this.hasAttribute('href'))) {
            return this;
        }

        return this.parent?.closest() ?? null;
    }
}

const setup = (enabled = true) => {
    const events = [];
    let listener;
    runInNewContext(source, {
        Element: TestElement,
        URL,
        window: {
            location: new URL('https://avxengineering.com/projects'),
            ...(enabled ? { gtag: (...args) => events.push(args) } : {}),
        },
        document: {
            addEventListener: (type, callback, options) => {
                assert.equal(type, 'click');
                assert.equal(options.capture, true);
                listener = callback;
            },
        },
    });

    return { events, click: (target, options = {}) => listener({ target, button: 0, ...options }) };
};

test('contact, quote and investor navigation produce distinct reports without query values', () => {
    const { events, click } = setup();
    for (const [href, name] of [
        ['/?email=private@example.com#quote', 'quote_click'],
        ['/#contact', 'contact_click'],
        ['/investment', 'investor_page_click'],
        ['/investment#investor-interest', 'investor_interest_click'],
        ['/#services', 'navigation_click'],
    ]) {
        click(new TestElement('a', { href }));
        assert.equal(events.at(-1)[1], name);
    }
    assert.equal(events.length, 5);
    assert.equal(events[0][2].link_path, '/#quote');
    assert.equal(JSON.stringify(events).includes('private@example.com'), false);
});

test('nested icons and dynamically added project controls are tracked once', () => {
    const { events, click } = setup();
    const gallery = new TestElement('button', { 'data-gallery-open': '', 'data-gallery-title': 'Bagsakan' });
    click(new TestElement('svg', {}, gallery));
    click(new TestElement('a', { href: 'https://bagsakanveggies.com/?secret=value', 'data-analytics-project': 'Bagsakan' }));
    click(new TestElement('button', { 'data-investment-open': '' }));
    click(new TestElement('a', { href: '/projects', 'data-project-collection-toggle': '' }), { defaultPrevented: true });

    assert.deepEqual(events.map((event) => event[1]), [
        'project_gallery_click', 'project_visit_click', 'investor_inquiry_click', 'project_collection_click',
    ]);
    assert.equal(events[0][2].project_name, 'Bagsakan');
    assert.equal(events[1][2].project_name, 'Bagsakan');
    assert.equal(JSON.stringify(events).includes('secret'), false);
});

test('email and phone clicks exclude addresses, numbers and message content', () => {
    const { events, click } = setup();
    click(new TestElement('a', { href: 'mailto:private@example.com?body=private-message' }));
    click(new TestElement('a', { href: 'tel:+639123456789' }));
    assert.deepEqual(events.map((event) => event[1]), ['email_click', 'phone_click']);
    assert.equal(JSON.stringify(events).includes('private'), false);
    assert.equal(JSON.stringify(events).includes('639123456789'), false);
});

test('unrelated outbound clicks are left to enhanced measurement; disabled controls and unsupported links are ignored', () => {
    const { events, click } = setup();
    const disabled = new TestElement('button', { 'data-investment-open': '' });
    disabled.disabled = true;
    for (const control of [
        disabled,
        new TestElement('a', { href: '/investment', 'aria-disabled': 'true' }),
        new TestElement('a', { href: 'https://facebook.com/avx' }),
        new TestElement('a', { href: 'javascript:void(0)' }),
        new TestElement('a', { href: 'http://[' }),
        new TestElement('button', { type: 'submit' }),
        new TestElement('div'),
    ]) {
        click(control);
    }
    click(new TestElement('a', { href: '/investment' }), { button: 2 });
    assert.equal(events.length, 0);
});

test('tracking is inert without the production Google tag', () => {
    const { events, click } = setup(false);
    click(new TestElement('a', { href: '/investment' }));
    assert.equal(events.length, 0);
});
