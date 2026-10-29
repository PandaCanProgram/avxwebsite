import './gallery';
import './flight-intro';
import './story';
import './project-showcase';
import './project-collection';
import { allowsEngagementMotion, openSendDialog, closeSendDialog } from './engagement';
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

document.querySelectorAll('[data-quote-form], [data-investment-form]').forEach((inquiryForm) => {
    const dialog = inquiryForm.parentElement.querySelector('[data-send-via-dialog]');

    if (!dialog) {
        return;
    }

    const businessEmail = 'avxengineeringsolutions@gmail.com';
    const viberNumber = '+639933619061';
    const facebookPage = 'avxengineeringsolutions';
    const viberDraftLimit = 100;
    const status = dialog.querySelector('[data-send-via-status]');
    const preview = dialog.querySelector('[data-send-via-preview]');
    let message = '';
    let subject = '';

    const showStatus = (text) => {
        status.textContent = text;
        status.classList.remove('hidden');
    };

    const copyMessage = async () => {
        try {
            await navigator.clipboard.writeText(message);

            return true;
        } catch {
            return false;
        }
    };

    const buildLinks = () => {
        const encodedMessage = encodeURIComponent(message);

        return {
            viber: message.length <= viberDraftLimit
                ? `viber://chat?number=${encodeURIComponent(viberNumber)}&draft=${encodedMessage}`
                : `viber://chat?number=${encodeURIComponent(viberNumber)}`,
            gmail: `https://mail.google.com/mail/?view=cm&fs=1&to=${businessEmail}&su=${encodeURIComponent(subject)}&body=${encodedMessage}`,
            facebook: `https://m.me/${facebookPage}?text=${encodedMessage}`,
        };
    };

    inquiryForm.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!inquiryForm.checkValidity()) {
            return;
        }

        const formData = new FormData(inquiryForm);
        const field = (name) => String(formData.get(name) ?? '').trim();
        const isInvestmentInquiry = inquiryForm.hasAttribute('data-investment-form');

        subject = isInvestmentInquiry
            ? `Investment Inquiry: ${field('name')}`
            : `Quote Request: ${field('subject')}`;
        const inquiryDetails = isInvestmentInquiry
            ? [
                ...(field('phone') ? [`Phone: ${field('phone')}`] : []),
                `Area of interest: ${field('interest')}`,
            ]
            : [`Subject: ${field('subject')}`];
        message = [
            'Hi AVX Engineering Solutions!',
            '',
            ...(isInvestmentInquiry ? ['I would like to discuss investing in AVX.', ''] : []),
            `Name: ${field('name')}`,
            `Email: ${field('email')}`,
            ...(field('organization') ? [`Company / Organization: ${field('organization')}`] : []),
            ...inquiryDetails,
            '',
            field('message'),
        ].join('\n');

        const links = buildLinks();

        dialog.querySelectorAll('[data-send-via]').forEach((option) => {
            const isAppLink = option.dataset.sendVia === 'viber';

            option.href = links[option.dataset.sendVia];

            if (isAppLink) {
                option.removeAttribute('target');
            } else {
                option.target = '_blank';
            }
        });

        preview.textContent = message;
        status.classList.add('hidden');
        openSendDialog(dialog);
        copyMessage();
    });

    dialog.querySelectorAll('[data-send-via]').forEach((option) => {
        option.addEventListener('click', async () => {
            const platform = option.dataset.sendVia;

            if (platform === 'gmail') {
                showStatus('Opening Gmail with your message filled in. Just hit send.');

                return;
            }

            const copied = await copyMessage();
            const appName = platform === 'viber' ? 'Viber' : 'Messenger';

            if (platform === 'viber' && message.length <= viberDraftLimit) {
                showStatus('Opening Viber with your message filled in. Just hit send.');

                return;
            }

            showStatus(copied
                ? `Your full message is copied. In ${appName}, paste it into the chat (Ctrl+V, or long-press then Paste) and send.`
                : `Opening ${appName}. Copy your message from the preview below and paste it there.`);
        });
    });

    dialog.querySelector('[data-send-via-copy]').addEventListener('click', async () => {
        showStatus(await copyMessage()
            ? 'Message copied to your clipboard.'
            : 'Copying is blocked by your browser. Select the preview text and copy it manually.');
    });

    dialog.querySelector('[data-send-via-close]').addEventListener('click', () => closeSendDialog(dialog));

    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeSendDialog(dialog);
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            const bounds = dialog.getBoundingClientRect();

            if (event.clientX < bounds.left || event.clientX > bounds.right
                || event.clientY < bounds.top || event.clientY > bounds.bottom) {
                closeSendDialog(dialog);
            }
        }
    });
});

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
            .scrollIntoView({ behavior: 'smooth', block: 'center' });
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
