const galleryDialog = document.querySelector('[data-gallery-dialog]');

if (galleryDialog) {
    const root = document.documentElement;
    const panel = galleryDialog.querySelector('[data-gallery-panel]');
    const imageFrame = galleryDialog.querySelector('[data-gallery-frame]');
    const galleryImage = galleryDialog.querySelector('[data-gallery-image]');
    const galleryTitle = galleryDialog.querySelector('[data-gallery-title]');
    const galleryCounter = galleryDialog.querySelector('[data-gallery-counter]');
    const thumbnails = galleryDialog.querySelector('[data-gallery-thumbnails]');
    const status = galleryDialog.querySelector('[data-gallery-status]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const animations = new Set();
    const photoAnimations = new Set();
    let images = [];
    let currentIndex = 0;
    let imageRequest = 0;
    let isClosing = false;
    let lastTrigger = null;
    let touchStart = null;
    let motionEnabled = true;

    const syncMotion = () => {
        motionEnabled = !reducedMotion.matches;
        if (!motionEnabled) {
            [...animations].forEach((animation) => animation.finish());
        }
    };

    const animate = (element, keyframes, options) => {
        if (!motionEnabled || typeof element.animate !== 'function') {
            return null;
        }
        const animation = element.animate(keyframes, options);
        animations.add(animation);
        animation.finished.then(() => {
            if (options.fill !== 'forwards') {
                animations.delete(animation);
            }
        }, () => animations.delete(animation));
        return animation;
    };

    const clearPhotoAnimations = () => {
        photoAnimations.forEach((animation) => animation.cancel());
        photoAnimations.clear();
        imageFrame.querySelectorAll('.gallery-outgoing').forEach((image) => image.remove());
    };

    const showImage = async (index, direction = 1, initial = false) => {
        if (!galleryDialog.open || isClosing || images.length === 0) {
            return;
        }
        const request = ++imageRequest;
        currentIndex = (index + images.length) % images.length;
        const selectedIndex = currentIndex;
        const source = images[selectedIndex];
        status.textContent = 'Loading photo…';
        status.hidden = false;
        imageFrame.setAttribute('aria-busy', 'true');

        [...thumbnails.children].forEach((thumbnail, thumbnailIndex) => {
            if (thumbnailIndex === selectedIndex) {
                thumbnail.setAttribute('aria-current', 'true');
                thumbnails.scrollTo({
                    left: thumbnail.offsetLeft - (thumbnails.clientWidth - thumbnail.clientWidth) / 2,
                    behavior: motionEnabled && !initial ? 'smooth' : 'instant',
                });
            } else {
                thumbnail.removeAttribute('aria-current');
            }
        });

        const loadedImage = new Image();
        loadedImage.src = source;
        try {
            await loadedImage.decode();
        } catch {
            if (request === imageRequest && galleryDialog.open && !isClosing) {
                status.textContent = 'This photo could not load. Select a thumbnail to try again.';
                imageFrame.setAttribute('aria-busy', 'false');
            }
            return;
        }
        if (request !== imageRequest || !galleryDialog.open || isClosing) {
            return;
        }

        clearPhotoAnimations();
        const outgoing = !galleryImage.hidden && galleryImage.getAttribute('src') !== source && motionEnabled
            ? galleryImage.cloneNode(false) : null;
        if (outgoing) {
            outgoing.removeAttribute('data-gallery-image');
            outgoing.alt = '';
            outgoing.setAttribute('aria-hidden', 'true');
            outgoing.classList.add('gallery-outgoing');
            imageFrame.insertBefore(outgoing, galleryImage);
        }
        galleryImage.src = source;
        galleryImage.alt = `${galleryTitle.textContent}, photo ${selectedIndex + 1}`;
        galleryImage.hidden = false;
        galleryCounter.textContent = `Photo ${selectedIndex + 1} of ${images.length}`;
        imageFrame.setAttribute('aria-busy', 'false');
        status.hidden = true;

        const incomingAnimation = animate(galleryImage, [
            { opacity: 0, transform: `translateX(${direction * 18}px) scale(0.985)` },
            { opacity: 1, transform: 'translateX(0px) scale(1)' },
        ], { duration: initial ? 420 : 320, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' });
        if (incomingAnimation) {
            photoAnimations.add(incomingAnimation);
        }
        if (outgoing) {
            const outgoingAnimation = animate(outgoing, [
                { opacity: 1, transform: 'translateX(0px) scale(1)' },
                { opacity: 0, transform: `translateX(${-direction * 18}px) scale(0.99)` },
            ], { duration: 240, easing: 'ease-out', fill: 'forwards' });
            if (outgoingAnimation) {
                photoAnimations.add(outgoingAnimation);
                outgoingAnimation.finished.then(() => outgoing.remove(), () => outgoing.remove());
            } else {
                outgoing.remove();
            }
        }
        [selectedIndex - 1, selectedIndex + 1].forEach((neighbor) => {
            const preload = new Image();
            preload.src = images[(neighbor + images.length) % images.length];
        });
    };

    const closeGallery = async () => {
        if (!galleryDialog.open || isClosing) {
            return;
        }
        isClosing = true;
        imageRequest++;
        [...animations].forEach((animation) => animation.cancel());
        clearPhotoAnimations();
        animate(panel, [
            { opacity: 1, transform: 'translateY(0px) scale(1)' },
            { opacity: 0, transform: 'translateY(12px) scale(0.98)' },
        ], { duration: 180, easing: 'ease-in', fill: 'forwards' });
        const exit = animate(galleryDialog, [{ opacity: 1 }, { opacity: 0 }], { duration: 220, easing: 'ease-out', fill: 'forwards' });
        try {
            await exit?.finished;
        } catch {
            // Interrupted exits still release the modal and scroll lock.
        } finally {
            galleryDialog.close();
        }
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-gallery-open]');

        if (!trigger) {
            return;
        }

        if (galleryDialog.open) {
            return;
        }
        images = JSON.parse(trigger.dataset.galleryImages);
        if (images.length === 0) {
            return;
        }
        syncMotion();
        lastTrigger = trigger;
        isClosing = false;
        currentIndex = 0;
        galleryTitle.textContent = trigger.dataset.galleryTitle;
        galleryDialog.classList.toggle('gallery-cutout', trigger.hasAttribute('data-gallery-cutout'));
        galleryCounter.textContent = `Photo 1 of ${images.length}`;
        const origin = trigger.getBoundingClientRect();

        thumbnails.replaceChildren(...images.map((source, index) => {
            const thumbnail = document.createElement('button');
            const image = document.createElement('img');
            thumbnail.type = 'button';
            thumbnail.className = 'gallery-thumbnail';
            thumbnail.setAttribute('aria-label', `Show photo ${index + 1}`);
            thumbnail.addEventListener('click', () => showImage(index, index < currentIndex ? -1 : 1));
            image.src = source;
            image.alt = '';
            image.decoding = 'async';
            thumbnail.append(image);
            return thumbnail;
        }));

        galleryDialog.showModal();
        root.classList.add('gallery-open');
        const destination = panel.getBoundingClientRect();
        const x = Math.max(-60, Math.min(60, (origin.left + origin.width / 2 - destination.left - destination.width / 2) * 0.12));
        const y = Math.max(-40, Math.min(40, (origin.top + origin.height / 2 - destination.top - destination.height / 2) * 0.12));
        animate(galleryDialog, [{ opacity: 0 }, { opacity: 1 }], { duration: 250, easing: 'ease-out' });
        animate(panel, [
            { opacity: 0, transform: `translate(${x}px, ${y + 16}px) scale(0.94)` },
            { opacity: 1, transform: 'translate(0px, 0px) scale(1)' },
        ], { duration: 480, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' });
        [...thumbnails.children].forEach((thumbnail, index) => animate(thumbnail, [
            { opacity: 0, transform: 'translateY(10px)' },
            { opacity: 1, transform: 'translateY(0px)' },
        ], { duration: 320, delay: Math.min(index * 40, 240) + 90, easing: 'ease-out', fill: 'backwards' }));
        showImage(0, 1, true);
    });

    galleryDialog.querySelector('[data-gallery-prev]').addEventListener('click', () => showImage(currentIndex - 1, -1));
    galleryDialog.querySelector('[data-gallery-next]').addEventListener('click', () => showImage(currentIndex + 1, 1));
    galleryDialog.querySelector('[data-gallery-close]').addEventListener('click', closeGallery);
    galleryDialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeGallery();
    });
    galleryDialog.addEventListener('close', () => {
        imageRequest++;
        [...animations].forEach((animation) => animation.cancel());
        animations.clear();
        clearPhotoAnimations();
        root.classList.remove('gallery-open');
        galleryImage.hidden = true;
        galleryImage.removeAttribute('src');
        status.hidden = true;
        isClosing = false;
        touchStart = null;
        lastTrigger?.focus({ preventScroll: true });
    });
    galleryDialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            const direction = event.key === 'ArrowLeft' ? -1 : 1;
            showImage(currentIndex + direction, direction);
        }
    });
    imageFrame.addEventListener('touchstart', (event) => {
        const touch = event.touches[0];
        touchStart = { x: touch.clientX, y: touch.clientY };
    }, { passive: true });
    imageFrame.addEventListener('touchend', (event) => {
        if (!touchStart) {
            return;
        }
        const touch = event.changedTouches[0];
        const x = touch.clientX - touchStart.x;
        const y = touch.clientY - touchStart.y;
        touchStart = null;
        if (Math.abs(x) > 45 && Math.abs(x) > Math.abs(y)) {
            const direction = x < 0 ? 1 : -1;
            showImage(currentIndex + direction, direction);
        }
    }, { passive: true });
    imageFrame.addEventListener('touchcancel', () => { touchStart = null; }, { passive: true });
    reducedMotion.addEventListener('change', syncMotion);
}
