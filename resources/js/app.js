import './gallery';
import './flight-intro';
import './story';
import './project-showcase';
import './project-collection';
import { allowsEngagementMotion } from './engagement';
import './investment';
import './portfolio';
import './page-transitions';

import.meta.glob(['../images/**/*.webp', '../images/portfolio-*.jpg'], { eager: true });

document.querySelectorAll('[data-nav-toggle]').forEach((toggle) => {
    const menu = document.getElementById(toggle.getAttribute('aria-controls'));

    toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', String(!isExpanded));
        menu.classList.toggle('hidden', isExpanded);
    });

    menu.querySelectorAll('a[href*="#"]').forEach((link) => {
        link.addEventListener('click', () => {
            toggle.setAttribute('aria-expanded', 'false');
            menu.classList.add('hidden');
        });
    });
});

document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-carousel-track]');
    const slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
    const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
    const autoplayDelay = 6000;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let currentIndex = 0;
    let autoplayTimer = null;
    let isVisible = false;

    if (slides.length < 2) {
        return;
    }

    const showSlide = (index) => {
        currentIndex = (index + slides.length) % slides.length;
        track.style.transform = `translateX(-${currentIndex * 100}%)`;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === currentIndex;

            slide.setAttribute('aria-hidden', String(!isActive));
            slide.inert = !isActive;
        });

        dots.forEach((dot, dotIndex) => {
            if (dotIndex === currentIndex) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
    };

    const stopAutoplay = () => {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    };

    const startAutoplay = () => {
        if (!allowsEngagementMotion() || autoplayTimer || !isVisible
            || document.hidden || carousel.matches(':hover') || carousel.contains(document.activeElement)) {
            return;
        }

        autoplayTimer = setInterval(() => showSlide(currentIndex + 1), autoplayDelay);
    };

    const restartAutoplay = () => {
        stopAutoplay();
        startAutoplay();
    };

    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
        showSlide(currentIndex - 1);
        restartAutoplay();
    });

    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
        showSlide(currentIndex + 1);
        restartAutoplay();
    });

    dots.forEach((dot, dotIndex) => {
        dot.addEventListener('click', () => {
            showSlide(dotIndex);
            restartAutoplay();
        });
    });

    let touchStartX = null;

    track.addEventListener('touchstart', (event) => {
        touchStartX = event.touches[0].clientX;
        stopAutoplay();
    }, { passive: true });

    track.addEventListener('touchend', (event) => {
        if (touchStartX === null) {
            return;
        }

        const swipeDistance = event.changedTouches[0].clientX - touchStartX;

        if (Math.abs(swipeDistance) > 50) {
            showSlide(currentIndex + (swipeDistance < 0 ? 1 : -1));
        }

        touchStartX = null;
        startAutoplay();
    });

    track.addEventListener('touchcancel', () => {
        touchStartX = null;
        startAutoplay();
    });

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('focusin', stopAutoplay);
    carousel.addEventListener('focusout', (event) => {
        if (!carousel.contains(event.relatedTarget)) {
            startAutoplay();
        }
    });

    showSlide(0);
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            isVisible = entry.isIntersecting;
            restartAutoplay();
        }, { threshold: 0.15 }).observe(carousel);
    } else {
        isVisible = true;
    }

    document.addEventListener('visibilitychange', restartAutoplay);
    reducedMotion.addEventListener('change', restartAutoplay);
    new MutationObserver(restartAutoplay).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    startAutoplay();
});

document.querySelectorAll('[data-feedback-form]').forEach((feedbackForm) => {
    feedbackForm.addEventListener('toggle', () => {
        if (!feedbackForm.open) {
            return;
        }

        feedbackForm.scrollIntoView({ behavior: allowsEngagementMotion() ? 'smooth' : 'auto', block: 'start' });
        feedbackForm.querySelector('input:not([type="hidden"]):not([tabindex="-1"])')?.focus({ preventScroll: true });
    });
});

const navLinks = [...document.querySelectorAll('[data-nav-link]')];
const navSections = [...new Set(navLinks.map((link) => new URL(link.href).hash))]
    .map((hash) => (hash ? document.querySelector(hash) : null))
    .filter(Boolean);

if (navSections.length > 0) {
    const highlightNavLink = (sectionId) => {
        navLinks.forEach((link) => {
            if (new URL(link.href).hash === `#${sectionId}`) {
                link.setAttribute('aria-current', 'true');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                highlightNavLink(entry.target.id);
            } else if (navLinks.some((link) => link.hasAttribute('aria-current') && new URL(link.href).hash === `#${entry.target.id}`)) {
                highlightNavLink(null);
            }
        });
    }, { rootMargin: '-45% 0px -50% 0px' });

    navSections.forEach((section) => sectionObserver.observe(section));
}

const fieldErrorMessage = (field) => {
    const { validity } = field;

    if (validity.valueMissing) {
        return field.dataset.requiredMessage ?? 'This field is required.';
    }

    if (validity.typeMismatch && field.type === 'email') {
        return 'Please enter a valid email address, like juan@email.com.';
    }

    if (validity.tooShort) {
        return `Please write at least ${field.minLength} characters.`;
    }

    if (validity.tooLong) {
        return `Please keep this under ${field.maxLength} characters.`;
    }

    return field.validationMessage;
};

document.querySelectorAll('[data-validate]').forEach((form) => {
    form.noValidate = true;

    const fields = [...form.querySelectorAll('input, textarea, select')]
        .filter((field) => field.willValidate && field.type !== 'hidden');

    const errorElementFor = (field) => {
        const existing = form.querySelector(`[data-field-error="${field.name}"]`);

        if (existing) {
            existing.id ||= `${field.name}-error`;

            return existing;
        }

        const errorElement = document.createElement('p');
        const anchor = field.type === 'radio' ? field.closest('fieldset').lastElementChild : field;

        errorElement.className = 'mt-2 text-sm text-red-600';
        errorElement.dataset.fieldError = field.name;
        errorElement.id = `${field.id || field.name}-error`;
        anchor.insertAdjacentElement('afterend', errorElement);

        return errorElement;
    };

    const fieldsNamed = (name) => fields.filter((field) => field.name === name);

    const setFieldError = (field, message) => {
        const errorElement = errorElementFor(field);

        errorElement.textContent = message;
        errorElement.hidden = message === '';

        fieldsNamed(field.name).forEach((namedField) => {
            if (message === '') {
                namedField.removeAttribute('aria-invalid');
                namedField.removeAttribute('aria-describedby');
            } else {
                namedField.setAttribute('aria-invalid', 'true');
                namedField.setAttribute('aria-describedby', errorElement.id);
            }
        });
    };

    const validateField = (field) => {
        const isValid = field.checkValidity();

        setFieldError(field, isValid ? '' : fieldErrorMessage(field));

        return isValid;
    };

    form.addEventListener('submit', (event) => {
        const checkedNames = new Set();
        const invalidFields = fields.filter((field) => {
            if (checkedNames.has(field.name)) {
                return false;
            }

            checkedNames.add(field.name);

            return !validateField(field);
        });

        if (invalidFields.length === 0) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const firstInvalidField = invalidFields[0];

        firstInvalidField.focus({ preventScroll: true });
        (firstInvalidField.type === 'radio' ? firstInvalidField.closest('fieldset') : firstInvalidField)
            .scrollIntoView({ behavior: allowsEngagementMotion() ? 'smooth' : 'auto', block: 'center' });
    });

    form.addEventListener('inquiry:validation-errors', (event) => {
        fields.forEach((field) => setFieldError(field, event.detail[field.name]?.[0] ?? ''));
        form.querySelector('[aria-invalid="true"]')?.focus();
    });

    form.addEventListener('reset', () => {
        fields.forEach((field) => setFieldError(field, ''));
    });

    fields.forEach((field) => {
        const clearWhenFixed = () => {
            if (field.getAttribute('aria-invalid') === 'true' || field.type === 'radio') {
                validateField(field);
            }
        };

        field.addEventListener('input', clearWhenFixed);
        field.addEventListener('change', clearWhenFixed);
    });
});

document.querySelectorAll('[data-inquiry-form]').forEach((form) => {
    const button = form.querySelector('button[type="submit"]');
    const label = button.querySelector('[data-submit-label]');
    const originalLabel = label.textContent;
    const fieldset = form.querySelector('fieldset');
    const status = form.querySelector('[data-inquiry-status]');
    let sending = false;

    const showStatus = (message, state) => {
        status.hidden = false;
        status.dataset.state = state;
        status.textContent = message;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (sending || !form.checkValidity()) {
            return;
        }

        const payload = new FormData(form);
        sending = true;
        button.disabled = true;
        fieldset.disabled = true;
        form.setAttribute('aria-busy', 'true');
        label.textContent = 'Sending…';
        showStatus('Submitting your inquiry…', 'pending');
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 30000);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: payload,
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal,
            });
            const result = await response.json().catch(() => ({}));

            if (response.ok && result.reference) {
                form.reset();
                fieldset.querySelectorAll('input, textarea, select').forEach((field) => {
                    if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    } else {
                        field.value = '';
                    }
                });
                const submissionId = form.elements.namedItem('submission_id');
                submissionId.value = result.next_submission_id;
                submissionId.defaultValue = result.next_submission_id;
                showStatus(`${result.message} Reference: ${result.reference}`, 'success');
            } else if (response.status === 422) {
                fieldset.disabled = false;
                form.dispatchEvent(new CustomEvent('inquiry:validation-errors', { detail: result.errors ?? {} }));
                const firstError = Object.values(result.errors ?? {})[0]?.[0];
                showStatus(firstError ?? 'Please review your details and try again.', 'error');
            } else if (response.status === 429) {
                showStatus('Too many inquiries were submitted. Please wait 10 minutes before trying again, or contact our team directly.', 'error');
            } else if (response.status === 419) {
                showStatus('Your session has expired. Please refresh this page before sending your inquiry.', 'error');
            } else {
                showStatus('We could not confirm receipt. Your details are still here; please retry, or email info@avxengineering.com.', 'error');
            }
        } catch {
            showStatus('We could not confirm receipt. Please check your connection and retry. Your details have been kept.', 'error');
        } finally {
            window.clearTimeout(timeout);
            sending = false;
            button.disabled = false;
            fieldset.disabled = false;
            form.removeAttribute('aria-busy');
            label.textContent = originalLabel;
        }
    });
});
