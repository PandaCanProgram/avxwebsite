@php
    $featuredProjects = collect(config('portfolio.projects'))->where('featured', true);
@endphp

<section id="projects" class="projects-scene project-showcase" data-scene data-scene-label="Featured Projects" data-project-showcase aria-labelledby="featured-projects-title">
    <div class="project-showcase-heading mx-auto max-w-7xl px-4 pt-16 sm:px-8 lg:pt-24">
        <p data-reveal class="scene-eyebrow font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Our Work</p>

        <div data-reveal class="mt-6 flex flex-wrap items-end justify-between gap-6">
            <h2 id="featured-projects-title" class="font-display text-5xl leading-[1.05] font-extrabold uppercase sm:text-6xl">Featured Projects</h2>
            <span class="project-showcase-count">{{ str_pad($featuredProjects->count(), 2, '0', STR_PAD_LEFT) }} selected projects</span>
        </div>
    </div>

    <div class="project-showcase-sequence" data-project-journey>
        <div class="project-sticky-stage">
            <div class="project-stage-inner mx-auto max-w-7xl px-4 sm:px-8">
                <div class="project-carousel-viewport" data-project-viewport>
                    @foreach ($featuredProjects as $project)
                        <div class="project-scroll-step" data-project-step aria-labelledby="featured-project-{{ $loop->iteration }}">
                            <x-site-project-card :project="$project" :showcase="true" :number="$loop->iteration" />
                        </div>
                    @endforeach
                </div>

                <div class="project-scroll-cue" aria-hidden="true"><span></span>Scroll to reveal</div>
                <div class="project-step-track" aria-hidden="true"><span></span></div>

                <nav class="project-carousel-controls" data-project-controls aria-label="Featured project selection" hidden>
                    <span class="project-carousel-position" aria-hidden="true"><span data-project-position>01</span> / {{ str_pad($featuredProjects->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    @foreach ($featuredProjects as $project)
                        <button type="button" data-project-select="{{ $loop->index }}" aria-label="Show project {{ $loop->iteration }}: {{ $project['title'] }}">
                            <span aria-hidden="true"></span>
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>

    <div class="project-archive mx-auto max-w-7xl px-4 sm:px-8" data-project-archive>
        <div class="project-archive-rule" aria-hidden="true"><span></span></div>
        <p data-reveal class="project-archive-kicker">Continue exploring <span>{{ count(config('portfolio.projects')) }} projects in the full collection</span></p>
        <a id="project-collection-toggle" data-reveal href="{{ route('projects') }}" class="project-archive-link" data-project-collection-toggle aria-expanded="false" aria-controls="project-collection">
            <span>View All Projects</span>
            <span class="project-archive-arrow" aria-hidden="true">
                <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.25"><path d="M6 26 26 6M7 6h19v19" /></svg>
            </span>
        </a>
        <ul class="project-archive-categories" aria-label="Portfolio categories">
            @foreach (config('portfolio.categories') as $category)
                <li>{{ $category }}</li>
            @endforeach
        </ul>

        <section id="project-collection" class="project-collection" data-project-collection aria-labelledby="project-collection-title" hidden inert>
            <div class="project-collection-inner" data-project-collection-inner>
                <div class="project-collection-toolbar">
                    <div>
                        <p class="project-collection-eyebrow">Explore the full collection</p>
                        <h3 id="project-collection-title" tabindex="-1">All Projects</h3>
                    </div>
                    <button type="button" class="project-collection-close" data-project-collection-close aria-label="Close all projects">
                        <span>Close</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M6 18 18 6" /></svg>
                    </button>
                </div>

                <div class="project-collection-loading" data-project-collection-loading>
                    <p data-project-collection-status role="status">Loading the project collection...</p>
                    <button type="button" class="project-collection-close" data-project-collection-retry hidden>Try again</button>
                </div>

                <div class="project-collection-mount" data-project-collection-mount></div>

                <div class="project-collection-bottom" data-project-collection-bottom hidden>
                    <span>End of collection</span>
                    <button type="button" class="project-collection-close" data-project-collection-close>
                        Close collection
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 14 6-6 6 6" /></svg>
                    </button>
                </div>
            </div>
        </section>
    </div>
</section>
