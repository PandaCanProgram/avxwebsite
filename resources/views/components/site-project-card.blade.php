@props([
    'project',
])

<article>
    <div class="flex aspect-[4/3] w-full items-center justify-center overflow-hidden bg-slate-800">
        @if ($project['image'])
            <img src="{{ Vite::asset($project['image']) }}" alt="{{ $project['title'] }}" loading="lazy" class="size-full {{ $project['imageFit'] }}">
        @else
            <div class="flex flex-col items-center gap-3 text-slate-400" aria-hidden="true">
                <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18" /><path d="m7 15 4-4 3 3 5-6" />
                </svg>
                <span class="font-display text-xs font-semibold tracking-[0.25em] uppercase">{{ $project['category'] }}</span>
            </div>
        @endif
    </div>

    <span class="mt-6 inline-block border border-brand px-2.5 py-1 font-display text-xs font-semibold tracking-[0.2em] text-brand uppercase">{{ $project['category'] }}</span>

    <h3 class="mt-4 font-display text-xl font-bold text-slate-900 uppercase">{{ $project['title'] }}</h3>
    <p class="mt-3 text-lg leading-relaxed text-gray-400">{{ $project['description'] }}</p>

    @isset($project['url'])
        <a href="{{ $project['url'] }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 font-medium text-brand hover:text-brand-dark">
            Visit site
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </a>
    @endisset
</article>
