const flightIntro = document.querySelector('[data-flight-intro]');
const navigation = performance.getEntriesByType('navigation')[0];

if (flightIntro && typeof flightIntro.showModal === 'function' && !window.location.hash && navigation?.type !== 'back_forward') {
    const root = document.documentElement;
    const startButton = flightIntro.querySelector('[data-flight-start]');
    const skipButton = flightIntro.querySelector('[data-flight-skip]');
    const status = flightIntro.querySelector('[data-flight-status]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let isStarting = false;
    let launchAnimation = null;
    let takeoffTimer;
    let motionDisabled = reducedMotion.matches;

    try {
        motionDisabled ||= sessionStorage.getItem('avx-scroll-motion') === 'off';
    } catch {
        // The entrance also works when browser storage is unavailable.
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

        if (motionDisabled || reducedMotion.matches) {
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
        window.dispatchEvent(new Event('avx:intro-complete'));

        const heading = document.querySelector('[data-story] h1');
        heading?.setAttribute('tabindex', '-1');
        heading?.focus({ preventScroll: true });
    }, { once: true });

    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) {
            flightIntro.dataset.motion = 'off';

            if (isStarting) {
                enterWebsite();
            }
        }
    });

    flightIntro.dataset.motion = motionDisabled ? 'off' : 'on';
    flightIntro.showModal();
    root.classList.add('flight-intro-open');
    startButton.focus({ preventScroll: true });
}
