import { allowsEngagementMotion } from './engagement';

const collectionToggle = document.querySelector('[data-project-collection-toggle]');
const collection = document.querySelector('[data-project-collection]');

if (collectionToggle && collection) {
    const root = document.documentElement;
    const inner = collection.querySelector('[data-project-collection-inner]');
    const mount = collection.querySelector('[data-project-collection-mount]');
    const loading = collection.querySelector('[data-project-collection-loading]');
    const status = collection.querySelector('[data-project-collection-status]');
    const retryButton = collection.querySelector('[data-project-collection-retry]');
    const bottom = collection.querySelector('[data-project-collection-bottom]');
    const heading = collection.querySelector('[data-project-collection-heading]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const itemAnimations = new Set();
    let expanded = false;
    let loaded = false;
    let loadPromise = null;
    let revision = 0;
    let heightAnimation = null;
    let scrollFrame = null;
    let finishScroll = null;

    const allowsMotion = allowsEngagementMotion;
    const revealObserver = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const item = entry.target;
            item.classList.remove('collection-reveal-pending');
            revealObserver.unobserve(item);

            if (allowsMotion() && typeof item.animate === 'function') {
                const animation = item.animate([
                    { opacity: 0, transform: 'translateY(20px)' },
                    { opacity: 1, transform: 'translateY(0px)' },
                ], {
                    duration: 480,
                    delay: Number(item.dataset.collectionDelay),
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    fill: 'backwards',
                });
                itemAnimations.add(animation);
                animation.finished.then(() => itemAnimations.delete(animation), () => itemAnimations.delete(animation));
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -16px 0px' }) : null;

    const prepareReveals = () => {
        revealObserver?.disconnect();
        itemAnimations.forEach((animation) => animation.cancel());
        itemAnimations.clear();
        mount.querySelectorAll('nav, section > h2, article').forEach((item, index) => {
            item.classList.remove('collection-reveal-pending');

            if (expanded && allowsMotion() && revealObserver) {
                item.dataset.collectionDelay = String((index % 3) * 55);
                item.classList.add('collection-reveal-pending');
                revealObserver.observe(item);
            }
        });
    };

    const animateHeight = async (open, currentRevision) => {
        const from = collection.hidden ? 0 : collection.getBoundingClientRect().height;
        heightAnimation?.cancel();
        heightAnimation = null;
        collection.hidden = false;
        const to = open ? inner.getBoundingClientRect().height : 0;
        collection.style.height = `${to}px`;

        if (allowsMotion() && typeof collection.animate === 'function' && Math.abs(to - from) > 1) {
            const animation = collection.animate([
                { height: `${from}px` },
                { height: `${to}px` },
            ], { duration: open ? 650 : 440, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' });
            heightAnimation = animation;

            try {
                await animation.finished;
            } catch {
                if (heightAnimation === animation) {
                    heightAnimation = null;
                }
                return false;
            }

            if (heightAnimation === animation) {
                heightAnimation = null;
            }
        }

        if (currentRevision !== revision) {
            return false;
        }

        collection.style.removeProperty('height');
        return true;
    };

    const loadCollection = () => {
        if (loaded) {
            return Promise.resolve(true);
        }

        if (loadPromise) {
            return loadPromise;
        }

        retryButton.hidden = true;
        status.textContent = 'Loading the project collection...';
        collection.setAttribute('aria-busy', 'true');
        loadPromise = (async () => {
            try {
                const response = await fetch(collectionToggle.href, {
                    credentials: 'same-origin',
                    headers: { Accept: 'text/html' },
                });

                if (!response.ok) {
                    throw new Error('The collection could not be loaded.');
                }

                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const content = page.querySelector('[data-project-collection-content]');

                if (!content) {
                    throw new Error('The collection is unavailable.');
                }

                const imported = document.importNode(content, true);
                imported.classList.add('project-collection-content');
                imported.querySelectorAll('section[id]').forEach((section) => {
                    const previousId = section.id;
                    section.id = `collection-${previousId}`;
                    imported.querySelectorAll(`a[href="#${previousId}"]`).forEach((link) => {
                        link.dataset.collectionTarget = section.id;
                        link.setAttribute('href', `${collectionToggle.href}#${previousId}`);
                    });
                });

                if (!collection.hidden && expanded) {
                    const currentHeight = collection.getBoundingClientRect().height;
                    heightAnimation?.cancel();
                    heightAnimation = null;
                    collection.style.height = `${currentHeight}px`;
                } else if (!collection.hidden && !heightAnimation) {
                    collection.style.height = `${collection.getBoundingClientRect().height}px`;
                }

                mount.replaceChildren(imported);
                mount.dispatchEvent(new CustomEvent('avx:portfolio-loaded', { bubbles: true }));
                loaded = true;
                loading.hidden = true;
                bottom.hidden = false;
                prepareReveals();
                return true;
            } catch {
                status.textContent = 'The collection could not load. Please try again.';
                retryButton.hidden = false;
                return false;
            } finally {
                collection.setAttribute('aria-busy', 'false');
                loadPromise = null;
            }
        })();

        return loadPromise;
    };

    const stopReturnScroll = () => {
        cancelAnimationFrame(scrollFrame);
        scrollFrame = null;
        finishScroll?.();
        finishScroll = null;
    };

    const openerPosition = () => {
        const headerHeight = parseFloat(getComputedStyle(root).getPropertyValue('--story-header-height')) || 80;
        return Math.max(0, window.scrollY + collectionToggle.getBoundingClientRect().top - headerHeight - 20);
    };

    const returnToOpener = (currentRevision) => new Promise((resolve) => {
        stopReturnScroll();
        const from = window.scrollY;
        const to = openerPosition();

        if (!allowsMotion() || Math.abs(to - from) < 2) {
            window.scrollTo({ top: to, behavior: 'instant' });
            resolve();
            return;
        }

        const start = performance.now();
        finishScroll = resolve;
        const scroll = (time) => {
            if (currentRevision !== revision) {
                stopReturnScroll();
                return;
            }

            const progress = Math.min(1, (time - start) / 440);
            const eased = 1 - (1 - progress) ** 3;
            window.scrollTo({ top: from + (to - from) * eased, behavior: 'instant' });

            if (progress < 1) {
                scrollFrame = requestAnimationFrame(scroll);
            } else {
                stopReturnScroll();
            }
        };
        scrollFrame = requestAnimationFrame(scroll);
    });

    const setExpanded = async (open) => {
        if (expanded === open) {
            return;
        }

        expanded = open;
        const currentRevision = ++revision;
        stopReturnScroll();
        collectionToggle.setAttribute('aria-expanded', String(open));
        root.classList.add('project-collection-expanded');

        if (open) {
            collection.inert = false;
            prepareReveals();
            animateHeight(true, currentRevision);
            collection.scrollIntoView({ behavior: allowsMotion() ? 'smooth' : 'instant', block: 'start' });
            await loadCollection();

            if (currentRevision !== revision || !expanded) {
                return;
            }

            if (await animateHeight(true, currentRevision)) {
                heading.focus({ preventScroll: true });
            }
        } else {
            collectionToggle.focus({ preventScroll: true });
            collection.inert = true;
            revealObserver?.disconnect();
            await returnToOpener(currentRevision);

            if (currentRevision !== revision || expanded) {
                return;
            }

            if (await animateHeight(false, currentRevision)) {
                collection.hidden = true;
                root.classList.remove('project-collection-expanded');
                prepareReveals();
            }
        }
    };

    collectionToggle.setAttribute('role', 'button');
    collectionToggle.addEventListener('click', (event) => {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        event.preventDefault();
        setExpanded(!expanded);
    });
    collectionToggle.addEventListener('keydown', (event) => {
        if (event.key === ' ') {
            event.preventDefault();

            if (!event.repeat) {
                setExpanded(!expanded);
            }
        }
    });
    collection.querySelectorAll('[data-project-collection-close]').forEach((button) => {
        button.addEventListener('click', () => setExpanded(false));
    });
    mount.addEventListener('click', (event) => {
        const link = event.target.closest('[data-collection-target]');

        if (!link || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        const target = document.getElementById(link.dataset.collectionTarget);

        if (target) {
            event.preventDefault();
            target.scrollIntoView({ behavior: allowsMotion() ? 'smooth' : 'instant', block: 'start' });
            target.setAttribute('tabindex', '-1');
            target.focus({ preventScroll: true });
        }
    });
    retryButton.addEventListener('click', async () => {
        const currentRevision = revision;
        await loadCollection();

        if (expanded && currentRevision === revision) {
            animateHeight(true, currentRevision);
        }
    });
    collection.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !document.querySelector('[data-gallery-dialog]')?.open) {
            event.preventDefault();
            setExpanded(false);
        }
    });

    const syncMotion = () => {
        if (!allowsMotion()) {
            heightAnimation?.finish();
            itemAnimations.forEach((animation) => animation.finish());
            revealObserver?.disconnect();
            mount.querySelectorAll('.collection-reveal-pending').forEach((item) => item.classList.remove('collection-reveal-pending'));

            if (scrollFrame !== null) {
                window.scrollTo({ top: openerPosition(), behavior: 'instant' });
                stopReturnScroll();
            }
        }
    };
    reducedMotion.addEventListener('change', syncMotion);
    new MutationObserver(syncMotion).observe(root, { attributes: true, attributeFilter: ['class'] });
}
