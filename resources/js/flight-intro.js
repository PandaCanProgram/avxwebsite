const flightIntro = document.querySelector('[data-flight-intro]');
const navigation = performance.getEntriesByType('navigation')[0];
const isReload = navigation?.type === 'reload';

if (flightIntro && typeof flightIntro.showModal === 'function' && (isReload || !window.location.hash) && navigation?.type !== 'back_forward') {
    const root = document.documentElement;
    const startButton = flightIntro.querySelector('[data-flight-start]');
    const skipButton = flightIntro.querySelector('[data-flight-skip]');
    const status = flightIntro.querySelector('[data-flight-status]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let isStarting = false;
    let launchAnimation = null;
    let takeoffTimer;

    if (isReload && window.location.hash) {
        history.replaceState(history.state, '', `${window.location.pathname}${window.location.search}`);
    }

    const enterWebsite = () => {
        clearTimeout(takeoffTimer);
        launchAnimation?.cancel();

        if (flightIntro.open) {
            flightIntro.close();
        }
    };

    const startDrone = async () => {
        if (isStarting) {
            return;
        }

        isStarting = true;
        startButton.disabled = true;
        window.scrollTo({ top: 0, behavior: 'instant' });

        if (reducedMotion.matches) {
            enterWebsite();

            return;
        }

        status.textContent = 'Spinning up…';
        flightIntro.classList.add('is-starting');
        takeoffTimer = setTimeout(() => {
            flightIntro.classList.add('is-airborne');
            status.textContent = 'Taking flight…';
        }, 550);

        try {
            launchAnimation = flightIntro.animate([
                { opacity: 1, offset: 0 },
                { opacity: 1, offset: 0.72 },
                { opacity: 0, offset: 1 },
            ], { duration: 1550, easing: 'ease-out', fill: 'forwards' });

            await launchAnimation.finished;
        } catch {
            // Skipping or cancelling the animation still opens the website.
        } finally {
            enterWebsite();
        }
    };

    startButton.addEventListener('click', startDrone);
    skipButton.addEventListener('click', enterWebsite);
    flightIntro.addEventListener('cancel', (event) => {
        event.preventDefault();
        enterWebsite();
    });

    flightIntro.addEventListener('close', () => {
        root.classList.remove('flight-intro-open');
        clearTimeout(takeoffTimer);
        window.scrollTo({ top: 0, behavior: 'instant' });
        window.dispatchEvent(new Event('avx:intro-complete'));

        const introduction = document.querySelector('[data-story] #intro');
        introduction?.setAttribute('tabindex', '-1');
        introduction?.focus({ preventScroll: true });
    }, { once: true });

    reducedMotion.addEventListener('change', () => {
        flightIntro.dataset.motion = reducedMotion.matches ? 'off' : 'on';

        if (reducedMotion.matches && isStarting) {
            enterWebsite();
        }
    });

    flightIntro.dataset.motion = reducedMotion.matches ? 'off' : 'on';
    flightIntro.showModal();
    root.classList.add('flight-intro-open');
    window.scrollTo({ top: 0, behavior: 'instant' });
    window.addEventListener('pageshow', () => {
        if (flightIntro.open) {
            window.scrollTo({ top: 0, behavior: 'instant' });
        }
    }, { once: true });
    startButton.focus({ preventScroll: true });
}
