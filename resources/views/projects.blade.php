@php
    $projectsByCategory = collect(config('portfolio.projects'))->groupBy('category');
@endphp

<x-site-layout title="Projects">
    <main class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
            <a href="{{ route('home') }}#projects" class="inline-flex items-center gap-2 text-gray-500 transition-colors hover:text-brand">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Home
            </a>

            <p class="mt-10 font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Our Work</p>

            <h1 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">All Projects</h1>

            <nav class="mt-10 flex flex-wrap gap-3" aria-label="Project categories">
                @foreach (config('portfolio.categories') as $category)
                    <a href="#{{ Str::slug($category) }}" class="border border-gray-200 px-4 py-2 text-gray-500 transition-colors hover:border-brand hover:text-brand">
                        {{ $category }}
                        <span class="ml-1 text-gray-400">({{ $projectsByCategory->get($category, collect())->count() }})</span>
                    </a>
                @endforeach
            </nav>

            @foreach (config('portfolio.categories') as $category)
                @if ($projectsByCategory->has($category))
                    <section id="{{ Str::slug($category) }}" class="mt-20 border-t border-gray-100 pt-16">
                        <h2 class="font-display text-3xl font-extrabold text-slate-900 uppercase sm:text-4xl">{{ $category }}</h2>

                        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($projectsByCategory->get($category) as $project)
                                <x-site-project-card :project="$project" />
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </main>

    <x-site-contact />
</x-site-layout>
