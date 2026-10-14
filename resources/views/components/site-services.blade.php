@php
    $services = [
        [
            'title' => 'CFD Simulation',
            'description' => 'Advanced aerodynamics, thermal analysis, and fluid flow optimization.',
            'icon' => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" /><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" /><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" />',
        ],
        [
            'title' => 'FEA Simulation',
            'description' => 'Structural integrity, stress, deformation, and mechanical analysis.',
            'icon' => '<rect x="3" y="3" width="7" height="7" /><rect x="14" y="3" width="7" height="7" /><rect x="3" y="14" width="7" height="7" /><rect x="14" y="14" width="7" height="7" /><path d="M10 6.5h4M10 17.5h4M6.5 10v4M17.5 10v4" />',
        ],
        [
            'title' => 'Custom Drone Design',
            'description' => 'Tailored multirotor and fixed-wing UAV development & integration.',
            'icon' => '<circle cx="5" cy="5" r="2" /><circle cx="19" cy="5" r="2" /><circle cx="5" cy="19" r="2" /><circle cx="19" cy="19" r="2" /><path d="M6.5 6.5 10 10M17.5 6.5 14 10M6.5 17.5 10 14M17.5 17.5 14 14" /><rect x="10" y="10" width="4" height="4" />',
        ],
        [
            'title' => 'Engineering Consultation',
            'description' => 'Technical guidance for engineering and innovation projects.',
            'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />',
        ],
        [
            'title' => 'Thesis Consultation',
            'description' => 'Professional support for undergraduate and graduate research.',
            'icon' => '<path d="M21.42 10.92a1 1 0 0 0-.02-1.84L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.83l8.57 3.91a2 2 0 0 0 1.66 0Z" /><path d="M22 10v6" /><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />',
        ],
        [
            'title' => 'Patent & IP Assistance',
            'description' => 'Innovation documentation and intellectual property guidance.',
            'icon' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z" /><path d="m9 12 2 2 4-4" />',
        ],
    ];
@endphp

<section id="services" class="bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">What We Do</p>

        <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Our Services</h2>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <div class="bg-white p-10">
                    <div class="flex size-14 items-center justify-center bg-brand/10 text-brand">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $service['icon'] !!}
                        </svg>
                    </div>

                    <h3 class="mt-10 font-display text-xl font-bold text-slate-900 uppercase">{{ $service['title'] }}</h3>
                    <p class="mt-3 text-lg leading-relaxed text-gray-400">{{ $service['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
