import { allowsEngagementMotion } from './engagement';

const investorUrl = document.body.dataset.investorUrl;

if (investorUrl) {
    const root = document.documentElement;
    const destination = new URL(investorUrl, window.location.href);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const supportsCrossDocumentTransitions = 'onpageswap' in window && 'onpagereveal' in window
        && window.CSS?.supports('view-transition-name', 'root');
    let navigationTimer = null;
    let restoreTimer = null;

    const clearTransition = () => {
        clearTimeout(navigationTimer);
        clearTimeout(restoreTimer);
        navigationTimer = null;
        restoreTimer = null;
        root.classList.remove('investor-route-leaving');
    };

    document.addEventListener('click', (event) => {
        const link = event.target instanceof Element ? event.target.closest('a[href]') : null;

        if (!link || event.defaultPrevented || event.button !== 0
            || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
            || link.hasAttribute('download') || (link.target && link.target !== '_self')) {
            return;
        }

        clearTransition();
        const target = new URL(link.href, window.location.href);

        if (target.origin !== destination.origin || target.pathname !== destination.pathname) {
            return;
        }

        document.querySelector('[data-nav-toggle]')?.setAttribute('aria-expanded', 'false');
        document.getElementById('mobile-nav')?.classList.add('hidden');

        if (target.pathname === window.location.pathname && target.search === window.location.search) {
            if (!target.hash) {
                event.preventDefault();
                history.replaceState(history.state, '', target.href);
                window.scrollTo({ top: 0, behavior: allowsEngagementMotion() ? 'smooth' : 'instant' });
            }
            return;
        }

        if (!allowsEngagementMotion() || supportsCrossDocumentTransitions) {
            return;
        }

        event.preventDefault();
        root.classList.add('investor-route-leaving');
        navigationTimer = setTimeout(() => {
            navigationTimer = null;
            window.location.assign(target.href);
        }, 180);
        restoreTimer = setTimeout(clearTransition, 4500);
    });

    window.addEventListener('pageshow', clearTransition);
    window.addEventListener('pagehide', clearTransition);
    window.addEventListener('pageswap', (event) => {
        if (reducedMotion.matches) {
            event.viewTransition?.skipTransition();
        }
    });
}
