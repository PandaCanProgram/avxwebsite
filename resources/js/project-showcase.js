const projectShowcase = document.querySelector('[data-project-showcase]');

if (projectShowcase && 'IntersectionObserver' in window) {
    const root = document.documentElement;
    const desktop = window.matchMedia('(min-width: 1100px) and (min-height: 760px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const journey = projectShowcase.querySelector('[data-project-journey]');
    const stage = journey.querySelector('.project-sticky-stage');
    const viewport = journey.querySelector('[data-project-viewport]');
    const steps = [...journey.querySelectorAll('[data-project-step]')];
    const controls = journey.querySelector('[data-project-controls]');
    const selectors = [...controls.querySelectorAll('[data-project-select]')];
    const position = controls.querySelector('[data-project-position]');
    const archive = projectShowcase.querySelector('[data-project-archive]');
    const cutoutModels = [...journey.querySelectorAll('[data-project-cutout]')];
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const introDuration = 0.9;
    const holdDuration = 0.5;
    const transitionDuration = 0.65;
    const totalTravel = introDuration + steps.length * holdDuration + Math.max(0, steps.length - 1) * transitionDuration;
    let enabled = false;
    let initialized = false;
    let frame = null;
    let journeyVisible = false;
    let archiveVisible = false;
    let activeIndex = -1;
    let pointerFrame = null;

    const resetCutoutTilt = () => {
        cancelAnimationFrame(pointerFrame);
        pointerFrame = null;
        cutoutModels.forEach((model) => {
            ['--cutout-tilt-x', '--cutout-tilt-y', '--cutout-move-x', '--cutout-move-y'].forEach((property) => model.style.removeProperty(property));
        });
    };

    journey.style.setProperty('--project-screens', String(1 + totalTravel));

    const clamp = (value) => Math.max(0, Math.min(1, value));
    const smoothStep = (value) => {
        const progress = clamp(value);

        return progress * progress * (3 - 2 * progress);
    };

    const updateActive = (index) => {
        if (activeIndex === index) {
            return;
        }

        activeIndex = index;
        resetCutoutTilt();
        position.textContent = String(index + 1).padStart(2, '0');
        steps.forEach((step, stepIndex) => {
            const active = stepIndex === index;
            step.inert = !active;
            step.setAttribute('aria-hidden', String(!active));
        });
        selectors.forEach((selector, selectorIndex) => {
            if (selectorIndex === index) {
                selector.setAttribute('aria-current', 'true');
            } else {
                selector.removeAttribute('aria-current');
            }
        });
    };

    const render = () => {
        frame = null;

        if (!enabled || document.hidden) {
            return;
        }

        const bounds = journeyVisible ? journey.getBoundingClientRect() : null;
        const archiveBounds = archiveVisible ? archive?.getBoundingClientRect() : null;
        const stageHeight = Math.max(1, stage.clientHeight);

        if (bounds) {
            const distance = Math.max(0, Math.min(totalTravel, (80 - bounds.top) / stageHeight));
            const slide = smoothStep(distance / introDuration);
            const copy = smoothStep((distance - 0.25) / 0.5);
            let cursor = 0;

            for (let index = 1; index < steps.length; index++) {
                const start = introDuration + holdDuration + (index - 1) * (transitionDuration + holdDuration);
                cursor += smoothStep((distance - start) / transitionDuration);
            }

            steps.forEach((step, index) => {
                step.style.setProperty('--feature-slide-y', `${((index - cursor) * 100).toFixed(3)}%`);
            });

            if (steps[0]) {
                steps[0].style.setProperty('--feature-media-x', `${(-33.333 * (1 - slide)).toFixed(3)}%`);
                steps[0].style.setProperty('--feature-media-scale', (1.22 - 0.22 * slide).toFixed(4));
                steps[0].style.setProperty('--feature-copy-opacity', copy.toFixed(4));
                steps[0].style.setProperty('--feature-copy-x', `${(-36 * (1 - copy)).toFixed(2)}px`);
            }

            journey.style.setProperty('--feature-cue-opacity', (1 - smoothStep(distance / introDuration)).toFixed(4));
            journey.style.setProperty('--feature-progress', clamp(distance / totalTravel).toFixed(4));
            updateActive(Math.min(steps.length - 1, Math.round(cursor)));
        }

        if (archiveBounds) {
            archive.style.setProperty('--archive-progress', smoothStep((window.innerHeight - archiveBounds.top) / (window.innerHeight * 0.65)).toFixed(4));
        }
    };

    const requestRender = () => {
        if (frame === null) {
            frame = requestAnimationFrame(render);
        }
    };

    const seekProject = (index, behavior = 'smooth') => {
        const targetIndex = Math.max(0, Math.min(steps.length - 1, index));
        const bounds = journey.getBoundingClientRect();
        const distance = introDuration + targetIndex * (transitionDuration + holdDuration) + holdDuration * 0.5;
        window.scrollTo({ top: window.scrollY + bounds.top - 80 + distance * stage.clientHeight, behavior });
        requestRender();
    };

    const syncMotion = () => {
        const nextEnabled = steps.length > 0 && desktop.matches && !reducedMotion.matches && root.classList.contains('story-motion-enabled');

        if (nextEnabled !== enabled) {
            const bounds = journey.getBoundingClientRect();
            const insideJourney = initialized && bounds.top <= window.innerHeight * 0.5 && bounds.bottom >= window.innerHeight * 0.5;
            const preserveIndex = insideJourney ? (enabled ? activeIndex : steps.findIndex((step) => {
                const stepBounds = step.getBoundingClientRect();

                return stepBounds.top <= window.innerHeight * 0.5 && stepBounds.bottom >= window.innerHeight * 0.5;
            })) : -1;

            enabled = nextEnabled;
            projectShowcase.classList.toggle('is-scroll-showcase', enabled);
            controls.hidden = !enabled;
            activeIndex = -1;

            if (enabled) {
                viewport.setAttribute('role', 'group');
                viewport.setAttribute('aria-roledescription', 'carousel');
                viewport.setAttribute('aria-label', 'Featured projects');
                steps.forEach((step) => {
                    step.setAttribute('role', 'group');
                    step.setAttribute('aria-roledescription', 'slide');
                });
                updateActive(Math.max(0, preserveIndex));
            } else {
                resetCutoutTilt();
                viewport.removeAttribute('role');
                viewport.removeAttribute('aria-roledescription');
                viewport.removeAttribute('aria-label');
                steps.forEach((step) => {
                    step.removeAttribute('style');
                    step.removeAttribute('role');
                    step.removeAttribute('aria-roledescription');
                    step.removeAttribute('aria-hidden');
                    step.inert = false;
                });
                archive?.style.removeProperty('--archive-progress');
            }

            if (preserveIndex >= 0) {
                if (enabled) {
                    seekProject(preserveIndex, 'instant');
                } else {
                    const stepBounds = steps[preserveIndex].getBoundingClientRect();
                    window.scrollTo({ top: window.scrollY + stepBounds.top - 80, behavior: 'instant' });
                }
            }
        }

        initialized = true;
        requestRender();
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.target === journey) {
                journeyVisible = entry.isIntersecting;
            } else if (entry.target === archive) {
                archiveVisible = entry.isIntersecting;
            }
        });
        requestRender();
    }, { rootMargin: '160px 0px' });

    observer.observe(journey);

    if (archive) {
        observer.observe(archive);
    }

    selectors.forEach((selector, index) => {
        selector.addEventListener('click', () => seekProject(index));
        selector.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') {
                return;
            }

            event.preventDefault();
            const nextIndex = Math.max(0, Math.min(steps.length - 1, index + (event.key === 'ArrowDown' ? 1 : -1)));
            selectors[nextIndex].focus({ preventScroll: true });
            seekProject(nextIndex);
        });
    });

    cutoutModels.forEach((model) => {
        let pointer = { x: 0, y: 0 };

        model.addEventListener('pointermove', (event) => {
            if (!enabled || !finePointer.matches) {
                return;
            }

            pointer = { x: event.clientX, y: event.clientY };

            if (pointerFrame !== null) {
                return;
            }

            pointerFrame = requestAnimationFrame(() => {
                pointerFrame = null;
                const bounds = model.getBoundingClientRect();
                const x = clamp((pointer.x - bounds.left) / bounds.width) - 0.5;
                const y = clamp((pointer.y - bounds.top) / bounds.height) - 0.5;

                model.style.setProperty('--cutout-tilt-x', `${(-y * 8).toFixed(2)}deg`);
                model.style.setProperty('--cutout-tilt-y', `${(x * 10).toFixed(2)}deg`);
                model.style.setProperty('--cutout-move-x', `${(x * 12).toFixed(2)}px`);
                model.style.setProperty('--cutout-move-y', `${(y * 8).toFixed(2)}px`);
            });
        });

        model.addEventListener('pointerleave', resetCutoutTilt);
    });

    projectShowcase.addEventListener('focusin', (event) => {
        if (enabled && event.target.closest('.project-feature-copy')) {
            const step = event.target.closest('[data-project-step]');
            const index = steps.indexOf(step);

            if (index >= 0) {
                seekProject(index, 'instant');
            }
        }
    });

    new MutationObserver(syncMotion).observe(root, { attributes: true, attributeFilter: ['class'] });
    desktop.addEventListener('change', syncMotion);
    reducedMotion.addEventListener('change', syncMotion);
    finePointer.addEventListener('change', resetCutoutTilt);
    window.addEventListener('blur', resetCutoutTilt);
    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    window.addEventListener('pageshow', requestRender);
    document.addEventListener('visibilitychange', requestRender);
    syncMotion();
}
