import.meta.glob(['../images/**'], { eager: true });

document.querySelectorAll('[data-nav-toggle]').forEach((toggle) => {
    const menu = document.getElementById(toggle.getAttribute('aria-controls'));

    toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', String(!isExpanded));
        menu.classList.toggle('hidden', isExpanded);
    });
});
