const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const root = document.documentElement;

export const allowsEngagementMotion = () => !reducedMotion.matches
    && (!root.classList.contains('story-ready') || root.classList.contains('story-motion-enabled'));

const dialogStates = new WeakMap();
const activeDialogs = new Set();

export const openSendDialog = (dialog) => {
    const previousState = dialogStates.get(dialog);

    previousState?.animations.forEach((animation) => animation.cancel());
    dialog.removeAttribute('data-closing');

    const state = { animations: [], closing: false };
    dialogStates.set(dialog, state);

    if (!dialog.open) {
        dialog.showModal();
    }

    activeDialogs.add(dialog);
    root.classList.add('send-via-open');

    if (!allowsEngagementMotion()) {
        return;
    }

    state.animations.push(dialog.animate([
        { opacity: 0, transform: 'translateY(24px) scale(0.96)', filter: 'blur(5px)' },
        { opacity: 1, transform: 'translateY(0) scale(1)', filter: 'blur(0)' },
    ], { duration: 450, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' }));

    dialog.querySelectorAll('[data-send-via]').forEach((option, index) => {
        state.animations.push(option.animate([
            { opacity: 0, transform: 'translateY(14px)' },
            { opacity: 1, transform: 'translateY(0)' },
        ], { duration: 380, delay: 100 + index * 65, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'backwards' }));
    });
};

export const closeSendDialog = async (dialog) => {
    const state = dialogStates.get(dialog);

    if (!dialog.open || state?.closing) {
        return;
    }

    const closingState = { animations: [], closing: true };
    dialogStates.set(dialog, closingState);
    state?.animations.forEach((animation) => animation.cancel());

    if (allowsEngagementMotion()) {
        dialog.setAttribute('data-closing', '');
        const animation = dialog.animate([
            { opacity: 1, transform: 'translateY(0) scale(1)' },
            { opacity: 0, transform: 'translateY(12px) scale(0.98)' },
        ], { duration: 220, easing: 'ease-in', fill: 'forwards' });
        closingState.animations.push(animation);
        await animation.finished.catch(() => {});
    }

    if (dialogStates.get(dialog) !== closingState) {
        return;
    }

    dialog.close();
};

document.querySelectorAll('[data-send-via-dialog]').forEach((dialog) => {
    dialog.addEventListener('close', () => {
        dialogStates.get(dialog)?.animations.forEach((animation) => animation.cancel());
        dialogStates.delete(dialog);
        dialog.removeAttribute('data-closing');
        activeDialogs.delete(dialog);
        root.classList.toggle('send-via-open', activeDialogs.size > 0);
    });
});

const faqControllers = [];

document.querySelectorAll('[data-faq-item]').forEach((item) => {
    const summary = item.querySelector('summary');
    const answer = item.querySelector('[data-faq-answer]');
    let animation = null;
    let wantsOpen = item.open;

    const settle = () => {
        animation?.cancel();
        animation = null;
        item.open = wantsOpen;
        item.style.removeProperty('height');
        answer.inert = !wantsOpen;
    };

    answer.inert = !item.open;
    faqControllers.push(settle);

    summary.addEventListener('click', (event) => {
        event.preventDefault();
        wantsOpen = !wantsOpen;

        if (!allowsEngagementMotion() || !item.animate) {
            settle();
            return;
        }

        const startHeight = item.getBoundingClientRect().height;
        animation?.cancel();
        item.style.height = `${startHeight}px`;
        item.open = true;
        answer.inert = !wantsOpen;

        const borderHeight = parseFloat(getComputedStyle(item).borderTopWidth)
            + parseFloat(getComputedStyle(item).borderBottomWidth);
        const endHeight = summary.getBoundingClientRect().height + borderHeight
            + (wantsOpen ? answer.getBoundingClientRect().height : 0);
        const nextAnimation = item.animate([
            { height: `${startHeight}px` },
            { height: `${endHeight}px` },
        ], { duration: 360, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'forwards' });
        animation = nextAnimation;
        nextAnimation.finished.then(() => {
            if (animation === nextAnimation) {
                settle();
            }
        }).catch(() => {});
    });
});

const settleMotion = () => {
    if (allowsEngagementMotion()) {
        return;
    }

    faqControllers.forEach((settle) => settle());
    activeDialogs.forEach((dialog) => {
        dialogStates.get(dialog)?.animations.forEach((animation) => animation.cancel());
    });
};

reducedMotion.addEventListener('change', settleMotion);
new MutationObserver(settleMotion).observe(root, { attributes: true, attributeFilter: ['class'] });
window.addEventListener('resize', () => faqControllers.forEach((settle) => settle()));
