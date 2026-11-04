import { allowsEngagementMotion } from './engagement';

const packagePage = document.querySelector('[data-drone-packages]');

if (packagePage) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const form = document.querySelector('[data-quote-form]');
    const subject = form?.elements.namedItem('subject');

    packagePage.querySelectorAll('[data-package-inquire]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (!subject || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            subject.value = `Drone package inquiry: ${link.dataset.packageTitle}`;
            subject.dispatchEvent(new Event('input', { bubbles: true }));
            const target = new URL(link.href);
            history.replaceState(history.state, '', `${target.pathname}${target.search}${target.hash}`);
            document.getElementById('contact').scrollIntoView({ behavior: allowsEngagementMotion() ? 'smooth' : 'auto', block: 'start' });
            form.elements.namedItem('name')?.focus({ preventScroll: true });
        });
    });

    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        document.body.classList.add('packages-motion-ready');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        packagePage.querySelectorAll('[data-package-reveal]').forEach((element) => observer.observe(element));
        reducedMotion.addEventListener('change', () => {
            if (reducedMotion.matches) {
                observer.disconnect();
                document.body.classList.remove('packages-motion-ready');
            }
        });
    }
}
