const story = document.querySelector('[data-story]');
const controls = document.querySelector('[data-story-controls]');

if (story && controls && 'IntersectionObserver' in window) {
    const root = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const desktopMotion = window.matchMedia('(min-width: 1100px) and (min-height: 760px)');
    const scenes = [...story.querySelectorAll('[data-scene]')];
    const chapterLinks = [...controls.querySelectorAll('[data-chapter-link]')];
    const progressBar = controls.querySelector('[data-story-progress]');
    const hero = story.querySelector('.hero-scene');
    const heroFrame = story.querySelector('[data-hero-frame]');
    const heroSurface = hero?.querySelector('.hero-visual');
    const heroPointer = hero?.querySelector('[data-hero-pointer]');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const visibleScenes = new Set();
    const parallaxLayers = new Map(scenes.map((scene) => [scene, [...scene.querySelectorAll('[data-parallax]')]]));
    const chapterId = (scene) => scene.dataset.chapterId || scene.id;
    let activeIndex = -1;
    let frame = null;
    let scrollRange = 1;
    let animationsEnabled = !reducedMotion.matches;
    let pointerFrame = null;
    let pointerPosition = null;

    const resetHeroPointer = () => {
        cancelAnimationFrame(pointerFrame);
        pointerFrame = null;
        pointerPosition = null;
        heroPointer?.removeAttribute('style');
    };

    const setActiveChapter = (index) => {
        if (activeIndex === index) {
            return;
        }

        activeIndex = index;
        chapterLinks.forEach((link) => {
            if (link.hash === `#${chapterId(scenes[index])}`) {
                link.setAttribute('aria-current', 'step');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const renderScroll = () => {
        frame = null;
        const viewportHeight = window.innerHeight;
        const anchor = Math.min(240, viewportHeight * 0.35);
        const measurements = [...visibleScenes].map((scene) => ({ scene, rect: scene.getBoundingClientRect() }));
        const current = measurements.reduce((best, item) => {
            const distance = item.rect.top <= anchor && item.rect.bottom > anchor
                ? 0
                : Math.min(Math.abs(item.rect.top - anchor), Math.abs(item.rect.bottom - anchor));

            return !best || distance < best.distance ? { ...item, distance } : best;
        }, null);

        progressBar.style.transform = `scaleX(${Math.max(0, Math.min(1, window.scrollY / scrollRange))})`;

        if (current) {
            setActiveChapter(scenes.indexOf(current.scene));
        }

        if (!animationsEnabled || document.hidden) {
            return;
        }

        const amplitude = desktopMotion.matches ? 1 : 0.28;

        measurements.forEach(({ scene, rect }) => {
            const distance = Math.max(-viewportHeight, Math.min(viewportHeight, viewportHeight * 0.5 - rect.top - rect.height * 0.5));

            parallaxLayers.get(scene).forEach((layer) => {
                const offset = distance * Number(layer.dataset.parallax) * amplitude;
                layer.style.setProperty('--parallax-y', `${offset.toFixed(2)}px`);
            });

            if (scene === hero && heroFrame) {
                const travel = Math.max(1, rect.height - viewportHeight + 80);
                const progress = Math.max(0, Math.min(1, (80 - rect.top) / travel));
                hero.style.setProperty('--hero-progress', progress.toFixed(4));
                heroFrame.style.setProperty('--hero-zoom', (1 + progress * 0.12 * amplitude).toFixed(4));
                heroFrame.style.setProperty('--hero-drift', `${(-progress * 30 * amplitude).toFixed(2)}px`);
                hero.style.setProperty('--orbit-angle', `${(progress * 35).toFixed(2)}deg`);
            }
        });
    };

    const requestRender = () => {
        if (frame === null) {
            frame = requestAnimationFrame(renderScroll);
        }
    };

    const measurePage = () => {
        scrollRange = Math.max(1, root.scrollHeight - window.innerHeight);
        requestRender();
    };

    const applyMotionPreference = () => {
        animationsEnabled = !reducedMotion.matches;
        root.classList.toggle('story-motion-enabled', animationsEnabled);

        if (!animationsEnabled) {
            resetHeroPointer();
            story.querySelectorAll('[data-parallax]').forEach((layer) => layer.style.removeProperty('--parallax-y'));
            hero?.style.removeProperty('--hero-progress');
            hero?.style.removeProperty('--orbit-angle');
            heroFrame?.style.removeProperty('--hero-zoom');
            heroFrame?.style.removeProperty('--hero-drift');
        }

        measurePage();
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const element = entry.target.matches('.hero-line') ? entry.target.firstElementChild : entry.target;
                element.classList.add('is-revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

    scenes.forEach((scene) => {
        const reveals = [...scene.querySelectorAll('[data-reveal]')];

        reveals.forEach((element, index) => {
            element.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 85}ms`);
        });
    });

    const observeReveals = () => {
        story.querySelectorAll('[data-reveal]').forEach((element) => {
            revealObserver.observe(element.closest('.hero-line') || element);
        });

        measurePage();
    };

    if (document.querySelector('[data-flight-intro]')?.open) {
        window.addEventListener('avx:intro-complete', observeReveals, { once: true });
    } else {
        observeReveals();
    }

    const sceneObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                visibleScenes.add(entry.target);
            } else {
                visibleScenes.delete(entry.target);
            }
        });

        requestRender();
    }, { rootMargin: '160px 0px', threshold: 0 });

    scenes.forEach((scene) => sceneObserver.observe(scene));

    const goToChapter = (index) => {
        const target = scenes[Math.max(0, Math.min(scenes.length - 1, index))];
        target.scrollIntoView({ behavior: animationsEnabled ? 'smooth' : 'instant', block: 'start' });

        // Keep chapter navigation reachable to keyboard and assistive technology.
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
    };

    document.querySelector('[data-home-link]')?.addEventListener('click', (event) => {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        event.preventDefault();
        document.querySelector('[data-nav-toggle]')?.setAttribute('aria-expanded', 'false');
        document.getElementById('mobile-nav')?.classList.add('hidden');
        history.replaceState(history.state, '', `${window.location.pathname}${window.location.search}`);
        goToChapter(0);
    });

    chapterLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const index = scenes.findIndex((scene) => link.hash === `#${chapterId(scene)}`);

            if (index >= 0) {
                event.preventDefault();
                goToChapter(index);
            }
        });
    });

    story.addEventListener('focusin', (event) => {
        event.target.closest('[data-reveal]')?.classList.add('is-revealed');
    });

    reducedMotion.addEventListener('change', applyMotionPreference);
    desktopMotion.addEventListener('change', measurePage);
    finePointer.addEventListener('change', resetHeroPointer);

    heroSurface?.addEventListener('pointermove', (event) => {
        if (!animationsEnabled || !finePointer.matches || !heroPointer || document.querySelector('[data-flight-intro]')?.open) {
            return;
        }

        pointerPosition = { x: event.clientX, y: event.clientY };

        if (pointerFrame !== null) {
            return;
        }

        pointerFrame = requestAnimationFrame(() => {
            pointerFrame = null;
            const bounds = heroSurface.getBoundingClientRect();
            const x = Math.max(-0.5, Math.min(0.5, (pointerPosition.x - bounds.left) / bounds.width - 0.5));
            const y = Math.max(-0.5, Math.min(0.5, (pointerPosition.y - bounds.top) / bounds.height - 0.5));

            heroPointer.style.setProperty('--hero-pointer-x', `${(x * 8).toFixed(2)}px`);
            heroPointer.style.setProperty('--hero-pointer-y', `${(y * 8).toFixed(2)}px`);
            heroPointer.style.setProperty('--hero-tilt-x', `${(-y * 3).toFixed(2)}deg`);
            heroPointer.style.setProperty('--hero-tilt-y', `${(x * 3).toFixed(2)}deg`);
            heroPointer.style.setProperty('--hero-light-x', `${((x + 0.5) * 100).toFixed(2)}%`);
            heroPointer.style.setProperty('--hero-light-y', `${((y + 0.5) * 100).toFixed(2)}%`);
        });
    });

    heroSurface?.addEventListener('pointerleave', resetHeroPointer);
    window.addEventListener('blur', resetHeroPointer);
    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', measurePage, { passive: true });
    window.addEventListener('pageshow', measurePage);
    document.addEventListener('visibilitychange', requestRender);

    if ('ResizeObserver' in window) {
        new ResizeObserver(measurePage).observe(story);
    }

    document.fonts?.ready.then(measurePage);
    controls.hidden = false;
    root.classList.add('story-ready');
    setActiveChapter(0);
    applyMotionPreference();
}
