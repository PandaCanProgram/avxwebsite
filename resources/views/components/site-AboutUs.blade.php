{{--
    Use the same black-and-blue AVX mark as the header, footer, intro, and favicon.
--}}
@props([
    'image' => null,
    'imageAlt' => 'AVX Engineering Solutions logo — Aviation, UAV, Systems',
])

@php
    $highlights = [
        ['title' => 'Modern Technologies', 'description' => 'Industry-standard tools and frameworks for scalable systems'],
        ['title' => 'Efficient Development', 'description' => 'Clean, maintainable, and optimized engineering'],
        ['title' => 'End-to-End Solutions', 'description' => 'From concept and design to integration and deployment'],
        ['title' => 'Technical Support', 'description' => 'Continuous assistance through development, testing, and launch'],
    ];
@endphp

<section id="about" class="about-scene scene-panel overflow-hidden bg-white" data-scene data-scene-label="About Us">
    <div class="scene-grid" aria-hidden="true" data-parallax="-0.08"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-4 py-16 sm:px-8 lg:grid-cols-2 lg:gap-20 lg:py-28">
        @if ($image)
            <div class="about-visual relative mr-4 mb-4 sm:mr-6 sm:mb-6" data-reveal="left">
                <div data-parallax="0.08">
                    <div class="absolute inset-0 translate-x-4 translate-y-4 border-2 border-brand sm:translate-x-6 sm:translate-y-6" aria-hidden="true"></div>

                    <div class="relative aspect-square overflow-hidden border border-blue-100 bg-white bg-gradient-to-br from-white via-blue-50/60 to-blue-100/70 p-8 shadow-2xl shadow-slate-900/10 sm:p-12">
                        <div class="absolute top-0 left-0 h-1 w-20 bg-brand" aria-hidden="true"></div>
                        <div class="absolute top-0 left-0 h-20 w-1 bg-brand" aria-hidden="true"></div>
                        <div class="absolute right-0 bottom-0 h-1 w-20 bg-brand" aria-hidden="true"></div>
                        <div class="absolute right-0 bottom-0 h-20 w-1 bg-brand" aria-hidden="true"></div>

                        <img src="{{ $image }}" alt="{{ $imageAlt }}" class="relative size-full object-contain">
                    </div>
                </div>
            </div>
        @else
            <div class="aspect-[4/5] bg-slate-800"></div>
        @endif

        <div>
            <p data-reveal class="scene-eyebrow font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">About Us</p>

            <h2 data-reveal class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">
                Precision Engineering
                <span class="block">You Can Trust</span>
            </h2>

            <div data-reveal class="mt-8 space-y-6 text-lg leading-relaxed text-gray-500 sm:text-xl">
                <p>
                    AVX Engineering Solutions in the Philippines designs and fabricates unmanned aircraft systems (UAS), remotely piloted aircraft systems (RPAS), unmanned surface vehicles (USV), and unmanned ground vehicles (UGV), including boats and rovers.
                </p>
                <p>
                    Our engineering and design work spans CFD simulation, finite element analysis, 3D CAD design, and product rendering. We also develop business websites, management systems, AI applications, and UAV and aviation software.
                </p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2">
                @foreach ($highlights as $highlight)
                    <div data-reveal class="highlight-card border-l-2 border-brand bg-gray-50 px-5 py-6">
                        <h3 class="font-display text-sm font-semibold tracking-[0.2em] text-slate-900 uppercase">{{ $highlight['title'] }}</h3>
                        <p class="mt-2 text-gray-500">{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
