@props([
    'image' => null,
    'imageAlt' => 'White quadcopter drone flying over a forest',
    'statValue' => 'F450 · F500',
    'statLabel' => 'Drone Platforms Built',
])

@php
    $taglines = ['Aviation', 'UAV', 'Systems'];
@endphp

<section class="overflow-hidden bg-white">
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-4 py-16 sm:px-8 lg:grid-cols-2 lg:gap-12 lg:py-28">
        <div>
            <p class="flex items-center gap-4 font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">
                <span class="h-0.5 w-8 bg-brand" aria-hidden="true"></span>

                @foreach ($taglines as $tagline)
                    @if (! $loop->first)
                        <span aria-hidden="true">|</span>
                    @endif

                    <span>{{ $tagline }}</span>
                @endforeach
            </p>

            <h1 class="mt-12 font-display text-6xl leading-[0.95] font-extrabold text-slate-900 uppercase sm:text-7xl lg:text-[6rem]">
                We Build
                <span class="block text-brand">Your Ideas</span>
                <span class="block">And Take Flight</span>
            </h1>

            <p class="mt-12 max-w-xl text-lg leading-relaxed text-gray-500 sm:text-[22px]">
                Precision in the air begins with precision in engineering. AVX Engineering Solutions designs and fabricates custom multirotor and fixed-wing UAVs, and builds modern software — from full-stack web applications to AI-powered systems.
            </p>

            <div class="mt-14 flex flex-wrap gap-5">
                <a href="#services" class="bg-brand px-10 py-4 text-lg font-medium text-white transition-colors hover:bg-brand-dark">Our Services</a>
                <a href="#quote" class="border border-brand px-10 py-4 text-lg text-brand transition-colors hover:bg-brand hover:text-white">Request a Quote</a>
            </div>
        </div>

        <div class="relative mx-auto w-full max-w-xl lg:mr-0">
            <div class="absolute -top-8 -right-4 bottom-1/4 left-1/4 bg-brand/5 sm:-right-8" aria-hidden="true"></div>
            <div class="absolute top-1/2 -bottom-8 -left-8 w-2/5 border border-gray-200" aria-hidden="true"></div>

            <div class="relative aspect-[37/40] bg-slate-800">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $imageAlt }}" class="size-full object-cover">
                @endif

                <div class="absolute bottom-8 left-8 bg-gray-100 px-6 py-5 sm:min-w-48">
                    <p class="font-display text-3xl font-bold text-brand">{{ $statValue }}</p>
                    <p class="mt-1 text-gray-500">{{ $statLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
