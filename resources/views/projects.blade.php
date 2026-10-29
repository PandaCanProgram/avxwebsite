@php
    $projectsByCategory = collect(config('portfolio.projects'))->groupBy('category');
    $fromInvestors = request()->query('from') === 'investors';
    $backUrl = $fromInvestors ? route('investment').'#investment-work' : route('home').'#projects';
@endphp

<x-site-layout title="Projects" page-class="portfolio-page">
    <a href="#portfolio-main" class="portfolio-skip">Skip to projects</a>
    <main id="portfolio-main" class="portfolio-main" data-portfolio-page tabindex="-1">
        <div class="portfolio-shell mx-auto max-w-7xl px-4 sm:px-8">
            <a href="{{ $backUrl }}" class="portfolio-back-link">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ $fromInvestors ? 'Back to Investors' : 'Back to Home' }}
            </a>

            <div class="portfolio-hero" data-portfolio-reveal>
                <div>
                    <p class="portfolio-eyebrow">AVX / Project collection</p>
                    <h1>All projects.<br><span>Ideas, made real.</span></h1>
                    <p class="portfolio-intro">Explore our work in drone fabrication, engineering simulation, product design, and software. Each project brings a different technical challenge into focus.</p>
                </div>
                <dl class="portfolio-facts">
                    <div><dt>Projects</dt><dd>{{ str_pad($projectsByCategory->flatten(1)->count(), 2, '0', STR_PAD_LEFT) }}</dd></div>
                    <div><dt>Disciplines</dt><dd>{{ str_pad($projectsByCategory->count(), 2, '0', STR_PAD_LEFT) }}</dd></div>
                </dl>
            </div>

            <div class="portfolio-search-tools" data-portfolio-search-tools hidden>
                <div class="portfolio-search-field">
                    <label for="portfolio-search">Find a project</label>
                    <div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" /><path d="m16 16 5 5" /></svg>
                        <input id="portfolio-search" type="search" placeholder="Search drones, CAD, simulation, software..." autocomplete="off" aria-controls="portfolio-collection" data-portfolio-search>
                    </div>
                </div>
                <div class="portfolio-search-summary">
                    <p role="status" aria-live="polite" aria-atomic="true" data-portfolio-results>{{ $projectsByCategory->flatten(1)->count() }} projects</p>
                    <button type="button" data-portfolio-reset>Show all projects</button>
                </div>
            </div>

            <div id="portfolio-collection" data-project-collection-content>
                <nav class="portfolio-category-nav mt-10 flex flex-wrap gap-3" aria-label="Project categories">
                    @foreach (config('portfolio.categories') as $category)
                        <a href="#{{ Str::slug($category) }}" class="border border-gray-200 px-4 py-2 text-gray-500 transition-colors hover:border-brand hover:text-brand" data-portfolio-category="{{ Str::slug($category) }}">
                            {{ $category }}
                            <span class="ml-1 text-gray-400">({{ $projectsByCategory->get($category, collect())->count() }})</span>
                        </a>
                    @endforeach
                </nav>

                @foreach (config('portfolio.categories') as $category)
                    @if ($projectsByCategory->has($category))
                        <section id="{{ Str::slug($category) }}" class="portfolio-category mt-20 border-t border-gray-100 pt-16" data-portfolio-section>
                            <h2 class="font-display text-3xl font-extrabold text-slate-900 uppercase sm:text-4xl" data-portfolio-reveal>{{ $category }}</h2>

                            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($projectsByCategory->get($category) as $project)
                                    <x-site-project-card :project="$project" data-portfolio-project data-portfolio-reveal />
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>

            <div class="portfolio-empty" data-portfolio-empty hidden><h2>No matching projects</h2><p>Try another search or choose a different category.</p></div>

            <div class="portfolio-next" data-portfolio-reveal>
                <div><p class="portfolio-eyebrow">Continue the conversation</p><h2>{{ $fromInvestors ? 'See where our work could lead.' : 'Have an idea of your own?' }}</h2></div>
                <a href="{{ $fromInvestors ? route('investment').'#investor-interest' : route('home').'#contact' }}">{{ $fromInvestors ? 'Connect with AVX' : 'Talk to our team' }}<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg></a>
            </div>
        </div>
    </main>

    <x-site-contact />
</x-site-layout>
