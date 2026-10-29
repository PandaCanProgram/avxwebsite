import { allowsEngagementMotion } from './engagement';

const portfolioPage = document.querySelector('[data-portfolio-page]');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

if (portfolioPage) {
    const search = portfolioPage.querySelector('[data-portfolio-search]');
    const reset = portfolioPage.querySelector('[data-portfolio-reset]');
    const results = portfolioPage.querySelector('[data-portfolio-results]');
    const empty = portfolioPage.querySelector('[data-portfolio-empty]');
    const categories = [...portfolioPage.querySelectorAll('[data-portfolio-category]')];
    const sections = [...portfolioPage.querySelectorAll('[data-portfolio-section]')].map((section) => ({
        element: section,
        cards: [...section.querySelectorAll('[data-portfolio-project]')].map((card) => ({
            element: card,
            text: card.textContent.toLocaleLowerCase(),
        })),
    }));
    const categoryIds = new Set(sections.map((section) => section.element.id));
    const initialCategory = window.location.hash.slice(1);
    let activeCategory = categoryIds.has(initialCategory) ? initialCategory : null;

    const applyFilters = () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visibleProjects = 0;

        sections.forEach(({ element, cards }) => {
            let visibleInSection = 0;
            cards.forEach((card) => {
                const visible = (!activeCategory || activeCategory === element.id)
                    && (!query || card.text.includes(query));
                card.element.hidden = !visible;
                visibleInSection += Number(visible);
            });
            element.hidden = visibleInSection === 0;
            visibleProjects += visibleInSection;
        });

        categories.forEach((category) => {
            category.setAttribute('aria-pressed', String(category.dataset.portfolioCategory === activeCategory));
        });
        results.textContent = `${visibleProjects} ${visibleProjects === 1 ? 'project' : 'projects'}${query ? ' matching your search' : ''}`;
        empty.hidden = visibleProjects > 0;
        reset.disabled = !activeCategory && query === '';
    };

    const updateCategoryHash = () => {
        const hash = activeCategory ? `#${activeCategory}` : '';
        history.replaceState(history.state, '', `${window.location.pathname}${window.location.search}${hash}`);
    };

    categories.forEach((category) => {
        category.setAttribute('role', 'button');
        category.setAttribute('aria-controls', category.dataset.portfolioCategory);
        const selectCategory = () => {
            activeCategory = activeCategory === category.dataset.portfolioCategory ? null : category.dataset.portfolioCategory;
            updateCategoryHash();
            applyFilters();
        };
        category.addEventListener('click', (event) => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
                return;
            }

            event.preventDefault();
            selectCategory();
        });
        category.addEventListener('keydown', (event) => {
            if (event.key === ' ') {
                event.preventDefault();
                if (!event.repeat) {
                    selectCategory();
                }
            }
        });
    });

    reset.addEventListener('click', () => {
        search.value = '';
        activeCategory = null;
        updateCategoryHash();
        applyFilters();
    });
    search.addEventListener('input', applyFilters);
    window.addEventListener('hashchange', () => {
        const hash = window.location.hash.slice(1);
        activeCategory = categoryIds.has(hash) ? hash : null;
        applyFilters();
    });
    portfolioPage.querySelector('[data-portfolio-search-tools]').hidden = false;
    applyFilters();

    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        const reveals = [...portfolioPage.querySelectorAll('[data-portfolio-reveal]')];
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });
        document.body.classList.add('portfolio-motion-ready');
        reveals.forEach((element, index) => {
            element.style.setProperty('--portfolio-reveal-delay', `${(index % 3) * 55}ms`);
            observer.observe(element);
        });
        portfolioPage.addEventListener('focusin', (event) => {
            event.target.closest('[data-portfolio-reveal]')?.classList.add('is-visible');
        });
        reducedMotion.addEventListener('change', () => {
            if (reducedMotion.matches) {
                observer.disconnect();
                document.body.classList.remove('portfolio-motion-ready');
            }
        });
    }
}

const interactiveCardSelector = '.investor-work-grid article, [data-portfolio-project]';
const interactiveCards = [...document.querySelectorAll(interactiveCardSelector)];

if (interactiveCards.length > 0 || document.querySelector('[data-project-collection]')) {
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const enhancedCards = new WeakSet();
    let activeCard = null;
    let pointer = null;
    let frame = null;

    const resetTilt = () => {
        cancelAnimationFrame(frame);
        frame = null;
        if (activeCard) {
            activeCard.style.removeProperty('--portfolio-tilt-x');
            activeCard.style.removeProperty('--portfolio-tilt-y');
            activeCard.classList.remove('portfolio-card-interacting');
        }
        activeCard = null;
    };

    const renderTilt = () => {
        frame = null;
        if (!activeCard || !finePointer.matches || !allowsEngagementMotion() || document.hidden || navigator.connection?.saveData) {
            resetTilt();
            return;
        }

        const bounds = activeCard.getBoundingClientRect();
        const x = Math.max(-0.5, Math.min(0.5, (pointer.x - bounds.left) / bounds.width - 0.5));
        const y = Math.max(-0.5, Math.min(0.5, (pointer.y - bounds.top) / bounds.height - 0.5));
        activeCard.style.setProperty('--portfolio-tilt-x', `${(-y * 4).toFixed(2)}deg`);
        activeCard.style.setProperty('--portfolio-tilt-y', `${(x * 6).toFixed(2)}deg`);
    };

    const enhanceCards = (container) => {
        container.querySelectorAll(interactiveCardSelector).forEach((card) => {
            if (enhancedCards.has(card)) {
                return;
            }
            enhancedCards.add(card);
            card.addEventListener('pointermove', (event) => {
                if (!finePointer.matches || !allowsEngagementMotion() || navigator.connection?.saveData) {
                    return;
                }
                if (activeCard !== card) {
                    resetTilt();
                    activeCard = card;
                    card.classList.add('portfolio-card-interacting');
                }
                pointer = { x: event.clientX, y: event.clientY };
                if (frame === null) {
                    frame = requestAnimationFrame(renderTilt);
                }
            });
            card.addEventListener('pointerleave', resetTilt);
        });
    };

    enhanceCards(document);
    document.addEventListener('avx:portfolio-loaded', (event) => enhanceCards(event.target));
    reducedMotion.addEventListener('change', resetTilt);
    finePointer.addEventListener('change', resetTilt);
    window.addEventListener('blur', resetTilt);
    window.addEventListener('scroll', resetTilt, { passive: true });
    document.addEventListener('visibilitychange', resetTilt);
}
