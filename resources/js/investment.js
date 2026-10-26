import { allowsEngagementMotion } from './engagement';

const investmentPage = document.querySelector('[data-investment-page]');

if (investmentPage) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const reveals = [...investmentPage.querySelectorAll('[data-investment-reveal]')];
    let frame = null;

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

        if (reducedMotion.matches || document.hidden) {
            investmentPage.style.removeProperty('--investment-drift');

            return;
        }

        const bounds = investmentPage.getBoundingClientRect();

        if (bounds.bottom > 0 && bounds.top < window.innerHeight) {
            const distance = Math.max(-60, Math.min(60, -bounds.top * 0.1));
            investmentPage.style.setProperty('--investment-drift', `${distance.toFixed(2)}px`);
        }
    };

    const requestRender = () => {
        if (frame === null) {
            frame = requestAnimationFrame(renderParallax);
        }
    };

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    window.addEventListener('pageshow', requestRender);
    reducedMotion.addEventListener('change', requestRender);
    document.addEventListener('visibilitychange', requestRender);
    requestRender();
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
