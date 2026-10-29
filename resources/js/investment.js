import { allowsEngagementMotion } from './engagement';

const investmentPage = document.querySelector('[data-investment-page]');

if (investmentPage) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const reveals = [...investmentPage.querySelectorAll('[data-investment-reveal]')];
    const sectionLinks = [...investmentPage.querySelectorAll('.investor-page-index a')];
    const desktopMotion = window.matchMedia('(min-width: 1024px) and (pointer: fine)');
    const parallaxScenes = new Map([...investmentPage.querySelectorAll('[data-investor-scene]')]
        .map((scene) => [scene, [...scene.querySelectorAll('[data-investor-parallax]')]]));
    const visibleScenes = new Set();
    let frame = null;
    let scrollRange = 1;

    if ('IntersectionObserver' in window && sectionLinks.length > 0) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                sectionLinks.forEach((link) => {
                    if (link.hash === `#${entry.target.id}`) {
                        link.setAttribute('aria-current', 'location');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
            });
        }, { rootMargin: '-15% 0px -55% 0px', threshold: 0 });

        sectionLinks.forEach((link) => {
            const section = investmentPage.querySelector(link.hash);
            if (section) {
                sectionObserver.observe(section);
            }
        });
    }

    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        document.body.classList.add('investment-motion-ready');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });

        reveals.forEach((element, index) => {
            element.style.setProperty('--investment-reveal-delay', `${Math.min(index % 4, 3) * 70}ms`);
            observer.observe(element);
        });

        investmentPage.addEventListener('focusin', (event) => {
            event.target.closest('[data-investment-reveal]')?.classList.add('is-visible');
        });

        reducedMotion.addEventListener('change', () => {
            if (reducedMotion.matches) {
                observer.disconnect();
                document.body.classList.remove('investment-motion-ready');
                reveals.forEach((element) => element.classList.add('is-visible'));
            }
        });
    }

    const renderParallax = () => {
        frame = null;

        if (document.hidden) {
            return;
        }

        investmentPage.style.setProperty('--investor-progress', String(Math.max(0, Math.min(1, window.scrollY / scrollRange))));

        if (reducedMotion.matches || navigator.connection?.saveData) {
            parallaxScenes.forEach((layers) => layers.forEach((layer) => layer.style.removeProperty('--investor-parallax-y')));

            return;
        }

        const viewportHeight = window.innerHeight;
        const amplitude = desktopMotion.matches ? 1 : 0.24;
        const measurements = [...visibleScenes].map((scene) => ({ scene, bounds: scene.getBoundingClientRect() }));

        measurements.forEach(({ scene, bounds }) => {
            const distance = Math.max(-viewportHeight, Math.min(viewportHeight,
                viewportHeight * 0.5 - bounds.top - Math.min(bounds.height, viewportHeight) * 0.5));

            parallaxScenes.get(scene).forEach((layer) => {
                const offset = Math.max(-64, Math.min(64, distance * Number(layer.dataset.investorParallax) * amplitude));
                layer.style.setProperty('--investor-parallax-y', `${offset.toFixed(2)}px`);
            });
        });
    };

    const requestRender = () => {
        if (frame === null) {
            frame = requestAnimationFrame(renderParallax);
        }
    };

    const measurePage = () => {
        scrollRange = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        requestRender();
    };

    if ('IntersectionObserver' in window) {
        const parallaxObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visibleScenes.add(entry.target);
                } else {
                    visibleScenes.delete(entry.target);
                }
            });
            requestRender();
        }, { rootMargin: '180px 0px' });
        parallaxScenes.forEach((layers, scene) => parallaxObserver.observe(scene));
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', measurePage, { passive: true });
    window.addEventListener('pageshow', measurePage);
    reducedMotion.addEventListener('change', requestRender);
    desktopMotion.addEventListener('change', requestRender);
    navigator.connection?.addEventListener('change', requestRender);
    document.addEventListener('visibilitychange', requestRender);
    if ('ResizeObserver' in window) {
        new ResizeObserver(measurePage).observe(investmentPage);
    }
    document.fonts?.ready.then(measurePage);
    measurePage();
}

const investmentDialog = document.querySelector('[data-investment-dialog]');

if (investmentDialog && typeof investmentDialog.showModal === 'function') {
    const root = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const animations = new Set();
    let lastTrigger = null;
    let operation = 0;
    let isClosing = false;
    let pointerStartedOutside = false;

    const clearAnimations = () => {
        animations.forEach((animation) => animation.cancel());
        animations.clear();
    };

    const animate = (element, keyframes, options) => {
        const animation = element.animate(keyframes, options);
        animations.add(animation);

        return animation;
    };

    const openInquiry = (trigger) => {
        operation += 1;
        clearAnimations();
        isClosing = false;
        investmentDialog.removeAttribute('data-closing');
        lastTrigger = trigger;

        if (!investmentDialog.open) {
            investmentDialog.showModal();
        }

        root.classList.add('investment-dialog-open');
        investmentDialog.scrollTop = 0;
        investmentDialog.querySelector('[data-investment-close]').focus({ preventScroll: true });

        if (!allowsEngagementMotion() || typeof investmentDialog.animate !== 'function') {
            return;
        }

        animate(investmentDialog, [
            { opacity: 0, transform: 'translateY(24px) scale(0.96)', filter: 'blur(4px)' },
            { opacity: 1, transform: 'translateY(0) scale(1)', filter: 'blur(0)' },
        ], { duration: 460, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' });

        investmentDialog.querySelectorAll('.investment-fields > div').forEach((field, index) => {
            animate(field, [
                { opacity: 0, transform: 'translateY(12px)' },
                { opacity: 1, transform: 'translateY(0)' },
            ], { duration: 360, delay: 90 + index * 35, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'backwards' });
        });
    };

    const closeInquiry = async () => {
        if (!investmentDialog.open || isClosing) {
            return;
        }

        isClosing = true;
        const closingOperation = ++operation;
        clearAnimations();

        if (allowsEngagementMotion() && typeof investmentDialog.animate === 'function') {
            investmentDialog.setAttribute('data-closing', '');
            const animation = animate(investmentDialog, [
                { opacity: 1, transform: 'translateY(0) scale(1)' },
                { opacity: 0, transform: 'translateY(16px) scale(0.98)' },
            ], { duration: 220, easing: 'ease-in', fill: 'forwards' });
            await animation.finished.catch(() => {});
        }

        if (closingOperation === operation) {
            investmentDialog.close();
        }
    };

    document.querySelectorAll('[data-investment-open]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            openInquiry(trigger);
        });
    });

    investmentDialog.querySelector('[data-investment-close]').addEventListener('click', closeInquiry);
    investmentDialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeInquiry();
    });

    const isOutside = (event) => {
        const bounds = investmentDialog.getBoundingClientRect();

        return event.target === investmentDialog
            && (event.clientX < bounds.left || event.clientX > bounds.right
                || event.clientY < bounds.top || event.clientY > bounds.bottom);
    };

    investmentDialog.addEventListener('pointerdown', (event) => {
        pointerStartedOutside = isOutside(event);
    });
    investmentDialog.addEventListener('click', (event) => {
        if (pointerStartedOutside && isOutside(event)) {
            closeInquiry();
        }

        pointerStartedOutside = false;
    });

    investmentDialog.addEventListener('close', () => {
        operation += 1;
        clearAnimations();
        isClosing = false;
        investmentDialog.removeAttribute('data-closing');
        root.classList.remove('investment-dialog-open');
        const sendDialog = investmentDialog.querySelector('[data-send-via-dialog]');

        if (sendDialog?.open) {
            sendDialog.close();
        }

        if (lastTrigger?.isConnected) {
            lastTrigger.focus({ preventScroll: true });
        }
    });

    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) {
            clearAnimations();
        }
    });
}
