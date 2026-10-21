import.meta.glob(['../images/**'], { eager: true });

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
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let currentIndex = 0;
    let autoplayTimer = null;

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
        if (prefersReducedMotion || autoplayTimer) {
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
        const swipeDistance = event.changedTouches[0].clientX - touchStartX;

        if (Math.abs(swipeDistance) > 50) {
            showSlide(currentIndex + (swipeDistance < 0 ? 1 : -1));
        }

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
    startAutoplay();
});

document.querySelectorAll('[data-feedback-form]').forEach((feedbackForm) => {
    feedbackForm.addEventListener('toggle', () => {
        if (!feedbackForm.open) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        feedbackForm.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
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

document.querySelectorAll('[data-quote-form]').forEach((quoteForm) => {
    const dialog = quoteForm.parentElement.querySelector('[data-send-via-dialog]');

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

    quoteForm.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!quoteForm.checkValidity()) {
            return;
        }

        const formData = new FormData(quoteForm);
        const field = (name) => String(formData.get(name) ?? '').trim();

        subject = `Quote Request: ${field('subject')}`;
        message = [
            'Hi AVX Engineering Solutions!',
            '',
            `Name: ${field('name')}`,
            `Email: ${field('email')}`,
            `Subject: ${field('subject')}`,
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
        dialog.showModal();
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

    dialog.querySelector('[data-send-via-close]').addEventListener('click', () => dialog.close());

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
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

const galleryDialog = document.querySelector('[data-gallery-dialog]');

if (galleryDialog) {
    const galleryImage = galleryDialog.querySelector('[data-gallery-image]');
    const galleryTitle = galleryDialog.querySelector('[data-gallery-title]');
    const galleryCounter = galleryDialog.querySelector('[data-gallery-counter]');
    const galleryThumbnails = galleryDialog.querySelector('[data-gallery-thumbnails]');
    let galleryImages = [];
    let galleryIndex = 0;
    let galleryTouchStartX = null;

    const showGalleryImage = (index) => {
        galleryIndex = (index + galleryImages.length) % galleryImages.length;
        galleryImage.src = galleryImages[galleryIndex];
        galleryImage.alt = `${galleryTitle.textContent}, photo ${galleryIndex + 1}`;
        galleryCounter.textContent = `Photo ${galleryIndex + 1} of ${galleryImages.length}`;

        [...galleryThumbnails.children].forEach((thumbnail, thumbnailIndex) => {
            const isActive = thumbnailIndex === galleryIndex;

            thumbnail.classList.toggle('opacity-100', isActive);
            thumbnail.classList.toggle('border-brand', isActive);
            thumbnail.classList.toggle('opacity-50', !isActive);
            thumbnail.classList.toggle('border-transparent', !isActive);

            if (isActive) {
                thumbnail.setAttribute('aria-current', 'true');
                thumbnail.scrollIntoView({ block: 'nearest', inline: 'center' });
            } else {
                thumbnail.removeAttribute('aria-current');
            }
        });
    };

    document.querySelectorAll('[data-gallery-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            galleryImages = JSON.parse(trigger.dataset.galleryImages);
            galleryTitle.textContent = trigger.dataset.galleryTitle;

            galleryThumbnails.replaceChildren(...galleryImages.map((source, index) => {
                const thumbnail = document.createElement('button');
                const thumbnailImage = document.createElement('img');

                thumbnail.type = 'button';
                thumbnail.className = 'h-14 w-20 shrink-0 overflow-hidden border-2 transition-opacity hover:opacity-100 sm:h-16 sm:w-24';
                thumbnail.setAttribute('aria-label', `Show photo ${index + 1}`);
                thumbnail.addEventListener('click', () => showGalleryImage(index));

                thumbnailImage.src = source;
                thumbnailImage.alt = '';
                thumbnailImage.className = 'size-full object-cover';
                thumbnail.append(thumbnailImage);

                return thumbnail;
            }));

            galleryDialog.showModal();
            document.documentElement.classList.add('overflow-hidden');
            showGalleryImage(0);
        });
    });

    galleryDialog.querySelector('[data-gallery-prev]').addEventListener('click', () => showGalleryImage(galleryIndex - 1));
    galleryDialog.querySelector('[data-gallery-next]').addEventListener('click', () => showGalleryImage(galleryIndex + 1));
    galleryDialog.querySelector('[data-gallery-close]').addEventListener('click', () => galleryDialog.close());

    galleryDialog.addEventListener('close', () => document.documentElement.classList.remove('overflow-hidden'));

    galleryDialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            showGalleryImage(galleryIndex - 1);
        } else if (event.key === 'ArrowRight') {
            showGalleryImage(galleryIndex + 1);
        }
    });

    galleryImage.addEventListener('touchstart', (event) => {
        galleryTouchStartX = event.touches[0].clientX;
    }, { passive: true });

    galleryImage.addEventListener('touchend', (event) => {
        const swipeDistance = event.changedTouches[0].clientX - galleryTouchStartX;

        if (Math.abs(swipeDistance) > 50) {
            showGalleryImage(galleryIndex + (swipeDistance < 0 ? 1 : -1));
        }
    });
}
