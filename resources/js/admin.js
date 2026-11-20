const themeForm = document.querySelector('[data-admin-theme-form]');

if (themeForm) {
    themeForm.addEventListener('change', (event) => {
        if (event.target.name === 'theme' && ['light', 'dark'].includes(event.target.value)) {
            document.documentElement.dataset.adminTheme = event.target.value;
        }
    });
}

document.querySelectorAll('[data-job-assignees]').forEach((group) => {
    const picker = group.querySelector('[data-job-participant-picker]');
    if (!picker) {
        return;
    }
    const switches = [...group.querySelectorAll('[data-job-participant]')];
    const trigger = group.querySelector('[data-job-participant-trigger]');
    const count = group.querySelector('[data-job-participant-count]');
    const error = group.querySelector('[data-job-participant-error]');
    const done = group.querySelector('[data-job-participant-done]');
    const empty = group.querySelector('[data-job-no-participants]');
    const badges = [...group.querySelectorAll('[data-job-selected-user]')];
    let attempted = !error.hidden;

    const updateChoices = () => {
        const selected = switches.filter((input) => input.checked);
        const ids = new Set(selected.map((input) => input.value));
        count.textContent = `${selected.length} selected`;
        badges.forEach((badge) => { badge.hidden = !ids.has(badge.dataset.jobSelectedUser); });
        empty.hidden = selected.length > 0;
        if (attempted) {
            const message = selected.length === 0 ? 'Choose at least one participant for this job order.' : '';
            error.textContent = message;
            error.hidden = message === '';
            trigger.setAttribute('aria-describedby', `job-assignees-help${message ? ' job-assignees-error' : ''}`);
            if (message) {
                trigger.setAttribute('aria-invalid', 'true');
            } else {
                trigger.removeAttribute('aria-invalid');
            }
        }
        return selected.length >= 1;
    };
    const closePicker = () => {
        picker.open = false;
        trigger.focus();
    };
    done.hidden = false;
    done.addEventListener('click', closePicker);
    group.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && picker.open) {
            event.preventDefault();
            closePicker();
        }
    });
    switches.forEach((input) => input.addEventListener('change', updateChoices));
    group.closest('form').addEventListener('submit', (event) => {
        attempted = true;
        if (!updateChoices()) {
            event.preventDefault();
            picker.open = true;
            trigger.focus();
        }
    });
    updateChoices();
});

document.querySelectorAll('[data-admin-user-form]').forEach((form) => {
    form.noValidate = true;
    const fields = [...form.querySelectorAll('input, select')].filter((field) => field.willValidate);
    const password = form.elements.namedItem('password');
    const confirmation = form.elements.namedItem('password_confirmation');
    let submitted = false;
    const requiredMessages = {
        name: "Please enter the user's full name.",
        email: 'Please enter an email address.',
        position: 'Please select Software or Hardware.',
        password: 'Please enter a password.',
        password_confirmation: 'Please confirm the password.',
    };

    const validationMessage = (field) => {
        const value = field.type === 'password' ? field.value : field.value.trim();
        if (field.required && !value) {
            return requiredMessages[field.name] ?? 'This field is required.';
        }
        if (field.name === 'password' && !value && confirmation.value) {
            return 'Please enter the new password.';
        }
        if (field.name === 'password_confirmation' && password.value && !value) {
            return 'Please confirm the password.';
        }
        if (field.name === 'password_confirmation' && value && value !== password.value) {
            return 'Passwords do not match.';
        }
        if (value && field.minLength > 0 && [...value].length < field.minLength) {
            return `Use at least ${field.minLength} characters.`;
        }
        if (field.maxLength > 0 && [...value].length > field.maxLength) {
            return `Use no more than ${field.maxLength} characters.`;
        }
        if (field.validity.typeMismatch) {
            return 'Please enter a valid email address.';
        }
        return field.checkValidity() ? '' : field.validationMessage;
    };

    const validateField = (field) => {
        const error = form.querySelector(`[data-admin-field-error="${field.name}"]`);
        const message = validationMessage(field);
        const descriptions = new Set((field.getAttribute('aria-describedby') ?? '').split(/\s+/).filter(Boolean));
        error.textContent = message;
        error.hidden = message === '';
        if (message) {
            field.setAttribute('aria-invalid', 'true');
            descriptions.add(error.id);
        } else {
            field.removeAttribute('aria-invalid');
            descriptions.delete(error.id);
        }
        if (descriptions.size) {
            field.setAttribute('aria-describedby', [...descriptions].join(' '));
        } else {
            field.removeAttribute('aria-describedby');
        }
        return message === '';
    };

    form.addEventListener('submit', (event) => {
        submitted = true;
        const invalidFields = fields.filter((field) => !validateField(field));
        if (invalidFields.length) {
            event.preventDefault();
            invalidFields[0].focus();
        }
    });

    fields.forEach((field) => {
        field.addEventListener('blur', () => validateField(field));
        const revalidate = () => {
            if (submitted || field.getAttribute('aria-invalid') === 'true') {
                validateField(field);
            }
            if (field === password && (submitted || confirmation.value || confirmation.getAttribute('aria-invalid') === 'true')) {
                validateField(confirmation);
            }
            if (field === confirmation && password.getAttribute('aria-invalid') === 'true') {
                validateField(password);
            }
        };
        field.addEventListener('input', revalidate);
        field.addEventListener('change', revalidate);
    });
});
