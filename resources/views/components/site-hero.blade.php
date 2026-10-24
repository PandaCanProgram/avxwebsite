@props([
    'image' => null,
    'imageAlt' => 'White quadcopter drone flying over a forest',
    'statValue' => 'F450 · F500',
    'statLabel' => 'Drone Platforms Built',
])

@php
    $taglines = ['Aviation', 'UAV', 'Systems'];
@endphp

<section id="intro" class="hero-scene" aria-labelledby="hero-title" data-scene data-scene-label="Introduction">
    <div class="hero-stage">
        <div class="scene-grid hero-grid" aria-hidden="true" data-parallax="-0.12"></div>
        <div class="hero-orbit" aria-hidden="true"><span></span><span></span><span></span></div>

        <div class="hero-content mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-8 lg:grid-cols-2 lg:gap-8">
            <div class="hero-copy">
                <p data-reveal class="hero-eyebrow flex items-center gap-4 font-display text-sm font-semibold tracking-[0.35em] uppercase">
                    <span class="h-0.5 w-8 bg-brand" aria-hidden="true"></span>

                    @foreach ($taglines as $tagline)
                        @if (! $loop->first)
                            <span aria-hidden="true">|</span>
                        @endif

                        <span>{{ $tagline }}</span>
                    @endforeach
                </p>

                <h1 id="hero-title" class="hero-title font-display font-extrabold uppercase">
                    <span class="hero-line"><span data-reveal>We Build</span></span>
                    <span class="hero-line"><span data-reveal class="hero-highlight">Your Ideas</span></span>
                    <span class="hero-line"><span data-reveal>And Take Flight</span></span>
                </h1>

                <p data-reveal class="hero-description max-w-xl text-lg leading-relaxed">
                    Precision in the air begins with precision in engineering. AVX Engineering Solutions designs and fabricates custom multirotor and fixed-wing UAVs, and builds modern software — from full-stack web applications to AI-powered systems.
                </p>

                <div data-reveal class="hero-actions flex flex-wrap gap-4">
                    <a href="#services" class="bg-brand px-10 py-4 text-lg font-medium text-white transition-colors hover:bg-brand-dark">Our Services</a>
                    <a href="#quote" class="border border-brand px-10 py-4 text-lg text-brand transition-colors hover:bg-brand hover:text-white">Request a Quote</a>
                </div>
            </div>

            <div class="hero-media-column">
                <div class="hero-visual" data-reveal="right">
                    <div class="hero-pointer-stage" data-hero-pointer>
                        <div class="hero-image-frame" data-hero-frame>
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $imageAlt }}" fetchpriority="high" width="1110" height="1200" class="hero-image size-full object-cover">
                            @endif

                            <div class="hero-image-shade" aria-hidden="true"></div>
                            <div class="hero-reticle" aria-hidden="true"></div>
                            <div class="hero-stat">
                                <span class="hero-stat-mark" aria-hidden="true"></span>
                                <div>
                                    <p class="font-display text-3xl font-bold text-white">{{ $statValue }}</p>
                                    <p class="mt-1 text-sm text-blue-100">{{ $statLabel }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="hero-pointer-sheen" aria-hidden="true"></div>
                    </div>
                    <span class="hero-frame-corner hero-frame-corner-start" aria-hidden="true"></span>
                    <span class="hero-frame-corner hero-frame-corner-end" aria-hidden="true"></span>
                </div>

                <x-site-scoring />
            </div>
        </div>

        <a href="#about" class="hero-scroll"><span class="hero-scroll-line" aria-hidden="true"></span>Scroll to explore<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 3v18m-6-6 6 6 6-6" /></svg></a>
    </div>
</section>
