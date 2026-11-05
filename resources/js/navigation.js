const siteNavigation = document.querySelector('[data-site-navigation]');

if (siteNavigation) {
    const header = siteNavigation.querySelector('.site-header');
    const root = document.documentElement;
    const viewport = window.visualViewport;
    const updateViewportOffset = () => {
        root.style.setProperty('--site-viewport-top', `${Math.max(0, viewport?.offsetTop ?? 0)}px`);
    };
    const updateNavigationHeight = () => {
        root.style.setProperty('--story-header-height', `${Math.ceil(siteNavigation.getBoundingClientRect().height)}px`);
        root.style.setProperty('--site-navbar-height', `${Math.ceil(header.getBoundingClientRect().height)}px`);
    };

    updateNavigationHeight();
    updateViewportOffset();
    if ('ResizeObserver' in window) {
        new ResizeObserver(updateNavigationHeight).observe(siteNavigation);
    } else {
        window.addEventListener('resize', updateNavigationHeight);
    }
    window.addEventListener('pageshow', updateNavigationHeight);
    window.addEventListener('pageshow', updateViewportOffset);
    viewport?.addEventListener('scroll', updateViewportOffset, { passive: true });
    viewport?.addEventListener('resize', updateViewportOffset, { passive: true });
}
