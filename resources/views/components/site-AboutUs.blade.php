{{--
    Image: upload the photo to resources/images/ (e.g. resources/images/about-us.png), then pass it in welcome.blade.php:
    <x-site-AboutUs :image="Vite::asset('resources/images/about-us.png')" />
    Run `npm run build` (or keep `npm run dev` running) so Vite picks up the new file.
--}}
@props([
    'image' => null,
    'imageAlt' => 'AVX Engineering Solutions logo — Aviation, UAV, Systems',
])

@php
    $highlights = [
        ['title' => 'CFD & FEA Expertise', 'description' => 'Industry-grade simulation for aerodynamics and structures'],
        ['title' => 'UAV Innovation', 'description' => 'Full-cycle drone design from concept to flight test'],
        ['title' => 'Research Support', 'description' => 'Thesis, capstone, and patent consultation'],
        ['title' => 'Technical Precision', 'description' => 'Documented, repeatable, peer-reviewed methodology'],
    ];
@endphp

<section id="about" class="overflow-hidden bg-white">
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-4 py-16 sm:px-8 lg:grid-cols-2 lg:gap-20 lg:py-28">
        @if ($image)
            <div class="relative mr-4 mb-4 sm:mr-6 sm:mb-6">
                <div class="absolute inset-0 translate-x-4 translate-y-4 border-2 border-brand sm:translate-x-6 sm:translate-y-6" aria-hidden="true"></div>

                <div class="relative aspect-square overflow-hidden border border-blue-100 bg-white bg-gradient-to-br from-white via-blue-50/60 to-blue-100/70 p-8 shadow-2xl shadow-slate-900/10 sm:p-12">
                    <div class="absolute top-0 left-0 h-1 w-20 bg-brand" aria-hidden="true"></div>
                    <div class="absolute top-0 left-0 h-20 w-1 bg-brand" aria-hidden="true"></div>
                    <div class="absolute right-0 bottom-0 h-1 w-20 bg-brand" aria-hidden="true"></div>
                    <div class="absolute right-0 bottom-0 h-20 w-1 bg-brand" aria-hidden="true"></div>

                    <img src="{{ $image }}" alt="{{ $imageAlt }}" class="relative size-full object-contain mix-blend-multiply">
                </div>
            </div>
        @else
            <div class="aspect-[4/5] bg-slate-800"></div>
        @endif

        <div>
            <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">About Us</p>

            <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">
                Precision Engineering
                <span class="block">You Can Trust</span>
            </h2>

            <div class="mt-8 space-y-6 text-lg leading-relaxed text-gray-500 sm:text-xl">
                <p>
                    AVX Engineering Solutions is a specialized firm focused on aviation, UAV systems, and computational analysis. We combine deep technical expertise with practical field experience to deliver results that matter.
                </p>
                <p>
                    Our team works with university students, independent researchers, and industry professionals — providing CFD/FEA simulations, drone prototyping, CAD services, and comprehensive technical consultation.
                </p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2">
                @foreach ($highlights as $highlight)
                    <div class="border-l-2 border-brand bg-gray-50 px-5 py-6">
                        <h3 class="font-display text-sm font-semibold tracking-[0.2em] text-slate-900 uppercase">{{ $highlight['title'] }}</h3>
                        <p class="mt-2 text-gray-500">{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
