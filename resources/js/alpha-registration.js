const alphaDialog = document.querySelector('[data-alpha-registration-dialog]');
const alphaTrigger = document.querySelector('[data-alpha-registration-open]');

if (alphaDialog && alphaTrigger && typeof alphaDialog.showModal === 'function') {
    const openRegistration = () => {
        if (alphaDialog.open) {
            return;
        }
        alphaDialog.showModal();
        document.documentElement.classList.add('alpha-registration-dialog-open');
        const focusTarget = alphaDialog.querySelector('[aria-invalid="true"]')
            ?? alphaDialog.querySelector('[data-state="success"]')
            ?? alphaDialog.querySelector('[name="alpha_name"]');
        focusTarget?.focus();
    };

    alphaTrigger.addEventListener('click', openRegistration);
    alphaDialog.querySelector('[data-alpha-registration-close]').addEventListener('click', () => alphaDialog.close());
    alphaDialog.addEventListener('close', () => {
        document.documentElement.classList.remove('alpha-registration-dialog-open');
        alphaTrigger.focus({ preventScroll: true });
    });
    alphaDialog.addEventListener('click', (event) => {
        if (event.target !== alphaDialog) {
            return;
        }
        const bounds = alphaDialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
            alphaDialog.close();
        }
    });

    if (alphaDialog.hasAttribute('data-alpha-registration-restore')) {
        alphaDialog.removeAttribute('open');
        openRegistration();
    }
}

document.querySelectorAll('[data-alpha-registration-form]').forEach((form) => {
    const name = form.elements.namedItem('alpha_name');
    const email = form.elements.namedItem('alpha_email');
    const button = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-alpha-registration-status]');
    const originalButton = button.innerHTML;
    let sending = false;

    const showStatus = (message, state) => {
        status.textContent = message;
        status.dataset.state = state;
        status.hidden = false;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (sending || !form.reportValidity()) {
            return;
        }

        sending = true;
        name.readOnly = true;
        email.readOnly = true;
        button.disabled = true;
        button.textContent = 'Registering…';
        form.setAttribute('aria-busy', 'true');
        email.removeAttribute('aria-invalid');
        name.removeAttribute('aria-invalid');
        showStatus('Saving your pre-registration…', 'pending');
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 15000);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
                signal: controller.signal,
            });
            const result = await response.json().catch(() => ({}));

            if (response.ok && result.message) {
                name.value = '';
                email.value = '';
                showStatus(result.message, 'success');
                status.focus();
            } else if (response.status === 422) {
                showStatus(Object.values(result.errors ?? {})[0]?.[0] ?? 'Please check your email and try again.', 'error');
                const invalidFields = [name, email].filter((field) => result.errors?.[field.name]);
                invalidFields.forEach((field) => field.setAttribute('aria-invalid', 'true'));
                if (invalidFields.length > 0) {
                    invalidFields[0].focus();
                }
            } else if (response.status === 429) {
                showStatus('Please wait a few minutes before trying again.', 'error');
            } else if (response.status === 419) {
                showStatus('Your session has expired. Please refresh this page and try again.', 'error');
            } else if (response.status === 404) {
                showStatus('Alpha-test pre-registration is now closed. Thank you for your interest.', 'error');
            } else {
                showStatus('We could not confirm your registration. Your email is still here; please try again.', 'error');
            }
        } catch {
            showStatus('Please check your connection and try again. Your email has been kept.', 'error');
        } finally {
            window.clearTimeout(timeout);
            sending = false;
            name.readOnly = false;
            email.readOnly = false;
            button.disabled = false;
            button.innerHTML = originalButton;
            form.removeAttribute('aria-busy');
        }
    });
});
