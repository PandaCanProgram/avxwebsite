@php
    $featuredProjects = collect(config('portfolio.projects'))->where('featured', true);
@endphp

<section id="projects" class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Our Work</p>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Featured Projects</h2>

            <a href="{{ route('projects') }}" class="border border-brand px-8 py-3 text-lg text-brand transition-colors hover:bg-brand hover:text-white">View All Projects</a>
        </div>

        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featuredProjects as $project)
                <x-site-project-card :project="$project" />
            @endforeach
        </div>
    </div>
</section>
