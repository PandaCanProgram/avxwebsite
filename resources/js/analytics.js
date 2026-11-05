const describeClick = (control) => {
    if (control.hasAttribute('data-investment-open')) {
        return { name: 'investor_inquiry_click' };
    }

    if (control.hasAttribute('data-project-collection-toggle')) {
        return { name: 'project_collection_click' };
    }

    if (control.hasAttribute('data-gallery-open')) {
        return {
            name: 'project_gallery_click',
            parameters: { project_name: control.dataset.galleryTitle },
        };
    }

    const href = control.getAttribute('href');

    if (!href) {
        return null;
    }

    let destination;

    try {
        destination = new URL(href, window.location.href);
    } catch {
        return null;
    }

    if (destination.protocol === 'mailto:') {
        return { name: 'email_click' };
    }

    if (destination.protocol === 'tel:') {
        return { name: 'phone_click' };
    }

    if (!['http:', 'https:'].includes(destination.protocol)) {
        return null;
    }

    if (control.hasAttribute('data-analytics-project')) {
        return {
            name: 'project_visit_click',
            parameters: { project_name: control.dataset.analyticsProject },
        };
    }

    if (destination.origin !== window.location.origin) {
        return null;
    }

    const sectionEvents = {
        '#quote': 'quote_click',
        '#contact': 'contact_click',
        '#investor-interest': 'investor_interest_click',
    };

    return {
        name: sectionEvents[destination.hash]
            ?? (destination.pathname === '/investment' ? 'investor_page_click' : 'navigation_click'),
        parameters: { link_path: destination.pathname + destination.hash },
    };
};

document.addEventListener('click', (event) => {
    if (typeof window.gtag !== 'function' || event.button !== 0 || !(event.target instanceof Element)) {
        return;
    }

    const control = event.target.closest('a[href], button');

    if (!control || control.disabled || control.getAttribute('aria-disabled') === 'true') {
        return;
    }

    const click = describeClick(control);

    if (click) {
        window.gtag('event', click.name, {
            ...click.parameters,
            page_path: window.location.pathname,
            transport_type: 'beacon',
        });
    }
}, { capture: true });
