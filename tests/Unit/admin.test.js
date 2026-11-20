import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/admin.js', import.meta.url), 'utf8');

const setup = (hasForm = true) => {
    const dataset = { adminTheme: 'light' };
    let change;
    const form = { addEventListener: (event, callback) => { change = callback; } };
    runInNewContext(source, {
        document: { querySelector: () => hasForm ? form : null, documentElement: { dataset } },
    });
    return { dataset, select: (value, name = 'theme') => change({ target: { name, value } }) };
};

test('appearance choices preview both themes without reloading', () => {
    const ui = setup();
    ui.select('dark');
    assert.equal(ui.dataset.adminTheme, 'dark');
    ui.select('light');
    assert.equal(ui.dataset.adminTheme, 'light');
});

test('unrecognized values and unrelated fields do not change the theme', () => {
    const ui = setup();
    ui.select('unsupported');
    ui.select('dark', 'other');
    assert.equal(ui.dataset.adminTheme, 'light');
});

test('admin pages without appearance settings keep their rendered preference', () => {
    assert.equal(setup(false).dataset.adminTheme, 'light');
});
