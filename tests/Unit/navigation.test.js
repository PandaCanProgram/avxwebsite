import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/navigation.js', import.meta.url), 'utf8');

const setup = (observed = false, bannerHeight = 53) => {
    const styles = new Map();
    const events = {};
    const viewportEvents = {};
    const viewport = {
        offsetTop: 0,
        addEventListener: (event, callback) => { viewportEvents[event] = callback; },
    };
    const header = { getBoundingClientRect: () => ({ height: 81 }) };
    const navigation = {
        bannerHeight,
        querySelector: () => header,
        getBoundingClientRect() { return { height: 81 + this.bannerHeight }; },
    };
    let observer;
    const context = {
        document: {
            querySelector: () => navigation,
            documentElement: { style: { setProperty: (key, value) => styles.set(key, value) } },
        },
        window: {
            visualViewport: viewport,
            addEventListener: (event, callback) => {
                const previous = events[event];
                events[event] = () => { previous?.(); callback(); };
            },
        },
    };
    if (observed) {
        const ResizeObserver = class {
            constructor(callback) { observer = callback; }
            observe(target) { assert.equal(target, navigation); }
        };
        context.ResizeObserver = ResizeObserver;
        context.window.ResizeObserver = ResizeObserver;
    }
    runInNewContext(source, context);
    return { styles, events, viewport, viewportEvents, navigation, resize: () => observed ? observer() : events.resize() };
};

test('fixed navigation reserves its full measured height including banner and borders', () => {
    const ui = setup();
    assert.equal(ui.styles.get('--story-header-height'), '134px');
    assert.equal(ui.styles.get('--site-navbar-height'), '81px');
    ui.navigation.bannerHeight = 69;
    ui.resize();
    assert.equal(ui.styles.get('--story-header-height'), '150px');
});

test('resize observation keeps offsets correct across mobile layout and font changes', () => {
    const ui = setup(true);
    ui.navigation.bannerHeight = 60.5;
    ui.resize();
    assert.equal(ui.styles.get('--story-header-height'), '142px');
    ui.navigation.bannerHeight = 53;
    ui.events.pageshow();
    assert.equal(ui.styles.get('--story-header-height'), '134px');
});

test('disabling the alpha banner reserves only the navbar height', () => {
    const ui = setup(false, 0);
    assert.equal(ui.styles.get('--story-header-height'), '81px');
    assert.equal(ui.styles.get('--site-navbar-height'), '81px');
});

test('mobile viewport scrolling keeps the complete navigation below the visible screen edge', () => {
    const ui = setup();
    assert.equal(ui.styles.get('--site-viewport-top'), '0px');
    ui.viewport.offsetTop = 54;
    ui.viewportEvents.scroll();
    assert.equal(ui.styles.get('--site-viewport-top'), '54px');
    assert.equal(ui.styles.get('--story-header-height'), '134px');
    ui.viewport.offsetTop = 0;
    ui.viewportEvents.resize();
    assert.equal(ui.styles.get('--site-viewport-top'), '0px');
});

test('overscroll cannot move the navigation above the visible screen', () => {
    const ui = setup();
    ui.viewport.offsetTop = -20;
    ui.viewportEvents.scroll();
    assert.equal(ui.styles.get('--site-viewport-top'), '0px');
    ui.viewport.offsetTop = 42;
    ui.events.pageshow();
    assert.equal(ui.styles.get('--site-viewport-top'), '42px');
});

test('browsers without the visual viewport API keep the standard fixed top position', () => {
    const styles = new Map();
    runInNewContext(source, {
        document: {
            querySelector: () => ({
                querySelector: () => ({ getBoundingClientRect: () => ({ height: 81 }) }),
                getBoundingClientRect: () => ({ height: 134 }),
            }),
            documentElement: { style: { setProperty: (key, value) => styles.set(key, value) } },
        },
        window: { addEventListener() {} },
    });
    assert.equal(styles.get('--site-viewport-top'), '0px');
});
