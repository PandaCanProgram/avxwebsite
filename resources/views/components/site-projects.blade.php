@php
    $projects = [
        [
            'category' => 'CFD Simulation',
            'title' => 'Fixed-Wing UAV Aerodynamic Analysis',
            'description' => 'Full CFD study across multiple angles of attack, yielding an 18% improved lift-to-drag ratio used in a capstone thesis.',
            'image' => null,
        ],
        [
            'category' => 'UAV Development',
            'title' => 'Custom Quadcopter Prototype',
            'description' => 'Frame design, component selection, PX4 flight controller integration, and documented field testing from ground up.',
            'image' => null,
        ],
        [
            'category' => 'FEA Analysis',
            'title' => 'Aircraft Bracket FEA Study',
            'description' => 'Static and dynamic FEA under combined loading conditions with topology optimization for 30% mass reduction.',
            'image' => null,
        ],
    ];
@endphp

<section id="projects" class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Our Work</p>

        <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Featured Projects</h2>

        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <article>
                    <div class="aspect-[37/20] w-full overflow-hidden bg-slate-800">
                        @if ($project['image'])
                            <img src="{{ $project['image'] }}" alt="{{ $project['title'] }}" class="size-full object-cover">
                        @endif
                    </div>

                    <span class="mt-6 inline-block border border-brand px-2.5 py-1 font-display text-xs font-semibold tracking-[0.2em] text-brand uppercase">{{ $project['category'] }}</span>

                    <h3 class="mt-4 font-display text-xl font-bold text-slate-900 uppercase">{{ $project['title'] }}</h3>
                    <p class="mt-3 text-lg leading-relaxed text-gray-400">{{ $project['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
