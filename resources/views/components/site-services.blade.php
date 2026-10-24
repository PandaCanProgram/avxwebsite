@php
    $serviceGroups = [
        [
            'title' => 'Drone Fabrication & UAV',
            'description' => 'Custom unmanned systems built for performance, reliability, and scalability — from stable, versatile multirotors to long-range, high-endurance fixed-wing platforms.',
            'applications' => ['Aerial Photography', 'Inspection', 'Mapping & Surveying', 'Surveillance', 'Monitoring', 'Agriculture', 'Delivery', 'Research'],
            'services' => [
                [
                    'title' => 'Custom UAV / Drone Fabrication',
                    'description' => 'Concept, design, and build of drones tailored to your mission requirements.',
                    'icon' => '<circle cx="5" cy="5" r="2" /><circle cx="19" cy="5" r="2" /><circle cx="5" cy="19" r="2" /><circle cx="19" cy="19" r="2" /><path d="M6.5 6.5 10 10M17.5 6.5 14 10M6.5 17.5 10 14M17.5 17.5 14 14" /><rect x="10" y="10" width="4" height="4" />',
                ],
                [
                    'title' => 'Fixed-Wing & Multirotor Development',
                    'description' => 'Airframes matched to the job — hover and maneuverability, or range and endurance.',
                    'icon' => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z" />',
                ],
                [
                    'title' => 'System Integration & Autopilot Setup',
                    'description' => 'Flight controllers, GPS, telemetry, and power systems wired and configured to work together.',
                    'icon' => '<rect x="6" y="6" width="12" height="12" rx="1" /><rect x="9.5" y="9.5" width="5" height="5" /><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4" />',
                ],
                [
                    'title' => 'Flight Performance Analysis',
                    'description' => 'Tuning and flight data review to get the stability and efficiency your mission needs.',
                    'icon' => '<path d="M3 3v18h18" /><path d="m7 15 4-4 3 3 5-6" />',
                ],
                [
                    'title' => 'Payload & Mission Planning',
                    'description' => 'Cameras, sensors, and flight plans set up for mapping, inspection, and surveillance.',
                    'icon' => '<path d="M9 4 3 6.5v13.5l6-2.5 6 2.5 6-2.5V4l-6 2.5z" /><path d="M9 4v13.5M15 6.5V20" />',
                ],
                [
                    'title' => 'Prototype Testing & Validation',
                    'description' => 'Ground and field testing to confirm every build performs before it ships.',
                    'icon' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z" /><path d="m9 12 2 2 4-4" />',
                ],
            ],
        ],
        [
            'title' => 'Programming Services',
            'description' => 'Modern, scalable, and reliable digital systems — software tailored to your operational and business needs.',
            'applications' => ['Business Websites', 'Management Systems', 'AI Applications', 'UAV & Aviation Software', 'Research Platforms', 'Industrial Automation', 'Educational Systems', 'Custom Web Applications'],
            'services' => [
                [
                    'title' => 'Full-Stack Web Development',
                    'description' => 'Complete front-end and back-end development for modern web applications.',
                    'icon' => '<path d="m16 18 6-6-6-6M8 6l-6 6 6 6" />',
                    'technologies' => ['HTML', 'CSS', 'Tailwind CSS', 'JavaScript', 'TypeScript', 'React', 'Next.js', 'FastAPI', 'Figma'],
                ],
                [
                    'title' => 'AI & Machine Learning',
                    'description' => 'Intelligent applications and machine learning solutions powered by Python.',
                    'icon' => '<rect x="5" y="5" width="14" height="14" rx="2" /><path d="M9 9h6v6H9zM9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3" />',
                    'technologies' => ['Python', 'PyTorch'],
                ],
            ],
        ],
    ];
@endphp

<section id="services" class="bg-gray-50">
    @foreach ($serviceGroups as $serviceGroup)
        <div id="{{ $loop->first ? 'drone-services' : 'programming' }}" class="service-scene scene-panel {{ $loop->first ? '' : 'scene-dark' }}" data-scene data-chapter-id="{{ $loop->first ? 'services' : 'programming' }}" data-scene-label="{{ $loop->first ? 'Drone & UAV' : 'Programming' }}">
            <div class="scene-grid" aria-hidden="true" data-parallax="-0.06"></div>
            <div class="scene-watermark" aria-hidden="true" data-parallax="0.1">{{ $loop->first ? 'UAV' : 'CODE' }}</div>
            <div class="service-content mx-auto w-full max-w-7xl px-4 py-16 sm:px-8 lg:py-24">
                @if ($loop->first)
                    <p data-reveal class="scene-eyebrow font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">What We Do</p>
                    <h2 data-reveal class="service-title mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Our Services</h2>
                @endif

                <div class="service-group {{ $loop->first ? 'mt-12' : '' }}">
                    <div class="service-overview grid gap-6 lg:grid-cols-2 lg:items-end lg:gap-12">
                        <div data-reveal>
                            <h3 class="font-display text-3xl font-extrabold text-slate-900 uppercase sm:text-4xl">{{ $serviceGroup['title'] }}</h3>
                            <p class="mt-4 text-lg leading-relaxed text-gray-500">{{ $serviceGroup['description'] }}</p>
                        </div>

                        <ul data-reveal class="flex flex-wrap gap-2 lg:justify-end" aria-label="{{ $serviceGroup['title'] }} applications">
                            @foreach ($serviceGroup['applications'] as $application)
                                <li class="border border-brand/30 bg-white px-3 py-1.5 text-sm text-brand">{{ $application }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="service-cards mt-10 grid gap-6 sm:grid-cols-2 {{ count($serviceGroup['services']) > 2 ? 'lg:grid-cols-3' : '' }}">
                        @foreach ($serviceGroup['services'] as $service)
                            <div data-reveal class="service-card bg-white p-10">
                                <div class="service-card-icon flex size-14 items-center justify-center bg-brand/10 text-brand">
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        {!! $service['icon'] !!}
                                    </svg>
                                </div>

                                <h4 class="mt-10 font-display text-xl font-bold text-slate-900 uppercase">{{ $service['title'] }}</h4>
                                <p class="mt-3 text-lg leading-relaxed text-gray-400">{{ $service['description'] }}</p>

                                @isset($service['technologies'])
                                    <ul class="mt-6 flex flex-wrap gap-2" aria-label="{{ $service['title'] }} technologies">
                                        @foreach ($service['technologies'] as $technology)
                                            <li class="bg-gray-100 px-2.5 py-1 font-display text-xs font-semibold tracking-[0.15em] text-slate-700 uppercase">{{ $technology }}</li>
                                        @endforeach
                                    </ul>
                                @endisset
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</section>
