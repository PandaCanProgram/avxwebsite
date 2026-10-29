@props([
    'project',
    'showcase' => false,
    'number' => 1,
])

@php
    $galleryImages = collect($project['gallery'] ?? [])->map(fn (string $image): string => Vite::asset($image));
@endphp

<article {{ $attributes->class(['project-feature-card' => $showcase]) }}>
    <div @class(['relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden bg-slate-800', 'project-feature-media' => $showcase, 'project-media-cutout' => $project['cutout'] ?? false]) @if ($project['cutout'] ?? false) data-project-cutout @endif>
        @if ($galleryImages->isNotEmpty())
            <button type="button" class="group size-full cursor-zoom-in" data-gallery-open data-gallery-title="{{ $project['title'] }}" data-gallery-images='@json($galleryImages)' @if ($project['cutout'] ?? false) data-gallery-cutout @endif aria-label="View {{ $galleryImages->count() }} photos of {{ $project['title'] }}">
                <img src="{{ Vite::asset($project['image']) }}" alt="{{ $project['title'] }}" loading="lazy" decoding="async" class="size-full transition-transform duration-500 group-hover:scale-105 {{ $project['imageFit'] }}">

                <span class="absolute right-3 bottom-3 inline-flex items-center gap-1.5 bg-slate-900/75 px-2.5 py-1.5 font-display text-xs font-semibold tracking-[0.15em] text-white uppercase backdrop-blur-sm transition-colors group-hover:bg-brand">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    {{ $galleryImages->count() }} Photos
                </span>
            </button>
        @elseif ($project['panelMockup'] ?? false)
            <div class="project-mockup-panel" style="--project-panel-ratio: {{ $project['panelAspectRatio'] ?? '429 / 202' }}">
                <img src="{{ Vite::asset($project['image']) }}" alt="{{ $project['title'] }}" loading="lazy" decoding="async" class="size-full object-contain">
            </div>
        @elseif ($project['image'])
            <img src="{{ Vite::asset($project['image']) }}" alt="{{ $project['title'] }}" loading="lazy" decoding="async" class="size-full {{ $project['imageFit'] }}">
        @else
            <div class="flex flex-col items-center gap-3 text-slate-400" aria-hidden="true">
                <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18" /><path d="m7 15 4-4 3 3 5-6" />
                </svg>
                <span class="font-display text-xs font-semibold tracking-[0.25em] uppercase">{{ $project['category'] }}</span>
            </div>
        @endif
    </div>

    <div @class(['project-feature-copy' => $showcase])>
        @if ($showcase)
            <p class="project-feature-number"><span aria-hidden="true">//</span> Project {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</p>
        @endif

        <span class="mt-6 inline-block border border-brand px-2.5 py-1 font-display text-xs font-semibold tracking-[0.2em] text-brand uppercase">{{ $project['category'] }}</span>

        <h3 @if ($showcase) id="featured-project-{{ $number }}" @endif class="mt-4 font-display text-xl font-bold text-slate-900 uppercase">{{ $project['title'] }}</h3>
        <p class="mt-3 text-lg leading-relaxed text-gray-400">{{ $project['description'] }}</p>

        @isset($project['url'])
            <a href="{{ $project['url'] }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 font-medium text-brand hover:text-brand-dark">
                Visit site
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @endisset
    </div>
</article>
