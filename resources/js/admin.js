const themeForm = document.querySelector('[data-admin-theme-form]');

if (themeForm) {
    themeForm.addEventListener('change', (event) => {
        if (event.target.name === 'theme' && ['light', 'dark'].includes(event.target.value)) {
            document.documentElement.dataset.adminTheme = event.target.value;
        }
    });
}
