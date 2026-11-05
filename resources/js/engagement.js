const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const root = document.documentElement;

export const getStickyHeaderHeight = () => parseFloat(getComputedStyle(root).getPropertyValue('--story-header-height')) || 80;

export const allowsEngagementMotion = () => !reducedMotion.matches
    && (!root.classList.contains('story-ready') || root.classList.contains('story-motion-enabled'));

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
};

reducedMotion.addEventListener('change', settleMotion);
new MutationObserver(settleMotion).observe(root, { attributes: true, attributeFilter: ['class'] });
window.addEventListener('resize', () => faqControllers.forEach((settle) => settle()));
