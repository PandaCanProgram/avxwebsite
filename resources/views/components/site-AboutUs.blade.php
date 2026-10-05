{{--
    Image: upload the photo to resources/images/ (e.g. resources/images/about-us.jpg), then pass it in welcome.blade.php:
    <x-site-AboutUs :image="Vite::asset('resources/images/about-us.jpg')" />
    Run `npm run build` (or keep `npm run dev` running) so Vite picks up the new file.
--}}
@props([
    'image' => null,
    'imageAlt' => 'Engineer reviewing a CAD drawing on a monitor',
    'statValue' => 'TBA',
    'statLabel' => 'Years delivering engineering excellence',
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
        <div class="relative mr-6 sm:mr-10">
            <div class="aspect-[4/5] bg-slate-800">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $imageAlt }}" class="size-full object-cover">
                @endif
            </div>

            <div class="absolute -right-6 -bottom-10 bg-brand px-8 py-7 shadow-xl sm:-right-10 sm:min-w-64">
                <p class="font-display text-5xl font-extrabold text-white">{{ $statValue }}</p>
                <p class="mt-2 max-w-44 leading-relaxed text-blue-100">{{ $statLabel }}</p>
            </div>
        </div>

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
