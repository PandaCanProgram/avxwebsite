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
        document: { querySelector: () => hasForm ? form : null, querySelectorAll: () => [], documentElement: { dataset } },
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

const setupParticipants = (selected = []) => {
    const inputs = ['1', '2', '3', '4'].map((value) => ({
        value, checked: selected.includes(value), disabled: false, events: {},
        addEventListener(event, callback) { this.events[event] = callback; },
    }));
    const badges = inputs.map((input) => ({ dataset: { jobSelectedUser: input.value }, hidden: true }));
    const attributes = new Map();
    const trigger = {
        setAttribute: (name, value) => attributes.set(name, value),
        removeAttribute: (name) => attributes.delete(name),
        focus() { this.focused = true; },
    };
    const picker = { open: false };
    const error = { id: 'job-assignees-error', hidden: true, textContent: '' };
    const count = { textContent: '' };
    const hint = { textContent: '' };
    const empty = { hidden: false };
    const doneEvents = {};
    const done = { hidden: true, addEventListener: (event, callback) => { doneEvents[event] = callback; } };
    const elements = { picker, trigger, count, error, hint, done };
    const formEvents = {};
    const groupEvents = {};
    const group = {
        querySelector: (selector) => selector === '[data-job-no-participants]' ? empty
            : elements[selector.match(/data-job-participant-([^\]]+)/)[1]],
        querySelectorAll: (selector) => selector === '[data-job-participant]' ? inputs : badges,
        closest: () => ({ addEventListener: (event, callback) => { formEvents[event] = callback; } }),
        addEventListener: (event, callback) => { groupEvents[event] = callback; },
    };
    runInNewContext(source, {
        document: {
            querySelector: () => null,
            querySelectorAll: (selector) => selector === '[data-job-assignees]' ? [group] : [],
        },
    });
    return {
        inputs, badges, trigger, picker, error, count, empty,
        change: (value, checked) => {
            const input = inputs.find((candidate) => candidate.value === value);
            input.checked = checked;
            input.events.change();
        },
        submit: () => {
            let prevented = false;
            formEvents.submit({ preventDefault: () => { prevented = true; } });
            return prevented;
        },
        done: () => doneEvents.click(),
        escape: () => {
            let prevented = false;
            groupEvents.keydown({ key: 'Escape', preventDefault: () => { prevented = true; } });
            return prevented;
        },
    };
};

test('participant switches update the count and selected names', () => {
    const ui = setupParticipants();
    assert.equal(ui.count.textContent, '0 selected');
    assert.equal(ui.empty.hidden, false);
    ui.change('1', true);
    assert.equal(ui.count.textContent, '1 selected');
    assert.equal(ui.badges[0].hidden, false);
    assert.equal(ui.empty.hidden, true);
    ui.change('1', false);
    assert.equal(ui.badges[0].hidden, true);
    assert.equal(ui.empty.hidden, false);
});

test('all users remain available when many participants are selected', () => {
    const ui = setupParticipants(['1', '2']);
    assert.equal(ui.inputs[0].disabled, false);
    assert.equal(ui.inputs[1].disabled, false);
    assert.equal(ui.inputs[2].disabled, false);
    ui.change('3', true);
    ui.change('4', true);
    assert.equal(ui.inputs.every((input) => !input.disabled), true);
    assert.equal(ui.count.textContent, '4 selected');
    assert.equal(ui.submit(), false);
    ui.change('1', false);
    assert.equal(ui.count.textContent, '3 selected');
    assert.equal(ui.badges[0].hidden, true);
});

test('empty selection blocks saving and opens the chooser with an inline error', () => {
    const ui = setupParticipants();
    assert.equal(ui.submit(), true);
    assert.equal(ui.picker.open, true);
    assert.equal(ui.trigger.focused, true);
    assert.equal(ui.error.hidden, false);
    ui.change('2', true);
    assert.equal(ui.error.hidden, true);
    assert.equal(ui.submit(), false);
});

test('many saved participants are restored and can be submitted', () => {
    const ui = setupParticipants(['1', '2', '3']);
    assert.equal(ui.inputs.filter((input) => input.checked).length, 3);
    assert.equal(ui.error.hidden, true);
    assert.equal(ui.submit(), false);
    ui.change('3', false);
    assert.equal(ui.error.hidden, true);
    assert.equal(ui.submit(), false);
});

test('Done and Escape close the chooser and restore focus without discarding participants', () => {
    const ui = setupParticipants(['1']);
    ui.picker.open = true;
    ui.done();
    assert.equal(ui.picker.open, false);
    assert.equal(ui.trigger.focused, true);
    ui.picker.open = true;
    assert.equal(ui.escape(), true);
    assert.equal(ui.picker.open, false);
    assert.equal(ui.inputs[0].checked, true);
});

const setupUserForm = (overrides = {}, editing = false) => {
    const defaults = { name: 'Alex Member', email: 'alex@example.test', position: 'Software', password: 'Abcd1234', password_confirmation: 'Abcd1234', ...overrides };
    const errors = {};
    const fields = Object.entries(defaults).map(([name, value]) => {
        const attributes = new Map(name === 'position' ? [['aria-describedby', 'user-position-help']] : []);
        errors[name] = { id: `${name}-error`, textContent: '', hidden: true };
        return {
            name, value, type: name.startsWith('password') ? 'password' : name === 'email' ? 'email' : 'text',
            required: !editing || !name.startsWith('password'), willValidate: true,
            minLength: name.startsWith('password') ? 8 : -1,
            maxLength: name.startsWith('password') ? 72 : 254,
            events: {},
            get validity() { return { typeMismatch: this.type === 'email' && !!this.value && !this.value.includes('@') }; },
            checkValidity: () => true,
            getAttribute: (key) => attributes.get(key) ?? null,
            setAttribute: (key, attributeValue) => attributes.set(key, attributeValue),
            removeAttribute: (key) => attributes.delete(key),
            addEventListener(event, callback) { this.events[event] = callback; },
            focus() { this.focused = true; },
        };
    });
    const byName = Object.fromEntries(fields.map((field) => [field.name, field]));
    const events = {};
    const form = {
        elements: { namedItem: (name) => byName[name] },
        querySelectorAll: () => fields,
        querySelector: (selector) => errors[selector.match(/data-admin-field-error="([^"]+)"/)[1]],
        addEventListener: (event, callback) => { events[event] = callback; },
    };
    runInNewContext(source, {
        document: {
            querySelector: () => null,
            querySelectorAll: (selector) => selector === '[data-admin-user-form]' ? [form] : [],
        },
    });
    return {
        form, fields: byName, errors,
        submit: () => {
            let prevented = false;
            events.submit({ preventDefault: () => { prevented = true; } });
            return prevented;
        },
    };
};

test('short passwords show an inline error and focus the red invalid field', () => {
    const ui = setupUserForm({ password: 'short', password_confirmation: 'short' });
    assert.equal(ui.form.noValidate, true);
    assert.equal(ui.submit(), true);
    assert.equal(ui.errors.password.textContent, 'Use at least 8 characters.');
    assert.equal(ui.errors.password.hidden, false);
    assert.equal(ui.fields.password.getAttribute('aria-invalid'), 'true');
    assert.equal(ui.fields.password.focused, true);
});

test('matching eight-character passwords are allowed to submit', () => {
    const ui = setupUserForm();
    assert.equal(ui.submit(), false);
    assert.equal(ui.errors.password.hidden, true);
    assert.equal(ui.errors.password_confirmation.hidden, true);
});

test('confirmation mismatch appears under the confirmation field', () => {
    const ui = setupUserForm({ password_confirmation: 'Different123' });
    assert.equal(ui.submit(), true);
    assert.equal(ui.errors.password_confirmation.textContent, 'Passwords do not match.');
    assert.equal(ui.fields.password_confirmation.getAttribute('aria-invalid'), 'true');
});

test('fixing passwords clears their inline errors while typing', () => {
    const ui = setupUserForm({ password: 'short', password_confirmation: 'short' });
    ui.submit();
    ui.fields.password.value = 'Abcd1234';
    ui.fields.password_confirmation.value = 'Abcd1234';
    ui.fields.password.events.input();
    ui.fields.password_confirmation.events.input();
    assert.equal(ui.errors.password.hidden, true);
    assert.equal(ui.errors.password_confirmation.hidden, true);
    assert.equal(ui.fields.password.getAttribute('aria-invalid'), null);
    assert.equal(ui.submit(), false);
});

test('an edit can leave both passwords blank but cannot enter confirmation alone', () => {
    const ui = setupUserForm({ password: '', password_confirmation: '' }, true);
    assert.equal(ui.submit(), false);
    ui.fields.password_confirmation.value = 'Abcd1234';
    assert.equal(ui.submit(), true);
    assert.equal(ui.errors.password.textContent, 'Please enter the new password.');
});

test('required fields and invalid emails still block submission without native popups', () => {
    const ui = setupUserForm({ name: '', email: 'invalid', position: '' });
    assert.equal(ui.submit(), true);
    assert.equal(ui.errors.name.hidden, false);
    assert.equal(ui.errors.email.textContent, 'Please enter a valid email address.');
    assert.equal(ui.errors.position.hidden, false);
    ui.fields.position.value = 'Software';
    ui.fields.position.events.change();
    assert.equal(ui.errors.position.hidden, true);
    assert.equal(ui.fields.position.getAttribute('aria-describedby'), 'user-position-help');
});
