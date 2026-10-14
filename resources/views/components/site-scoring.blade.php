@props([
    'stats' => [
        [
            'value' => '50+',
            'label' => 'Projects',
            'icon' => '<rect x="4" y="4" width="16" height="16" rx="3" /><path d="m8.5 12 2.5 2.5 4.5-5" />',
        ],
        [
            'value' => '20+',
            'label' => 'Drone Systems',
            'icon' => '<circle cx="5.5" cy="5.5" r="2.5" /><circle cx="18.5" cy="5.5" r="2.5" /><circle cx="5.5" cy="18.5" r="2.5" /><circle cx="18.5" cy="18.5" r="2.5" /><path d="m7.5 7.5 2.5 2.5m6.5-2.5L14 10m-6.5 6.5L10 14m6.5 2.5L14 14" /><rect x="10" y="10" width="4" height="4" rx="1" />',
        ],
        [
            'value' => '100+',
            'label' => 'Consultations',
            'icon' => '<path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7l-4 4v-4H6a2 2 0 0 1-2-2z" /><path d="M8 8.5h8M8 11.5h5" />',
        ],
        [
            'value' => '98%',
            'label' => 'Client Satisfaction',
            'icon' => '<path d="m12 3.5 2.6 5.3 5.9.9-4.25 4.1 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.7l5.9-.9z" />',
        ],
    ],
])

<section class="relative isolate overflow-hidden bg-gradient-to-br from-brand to-brand-dark">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgb(255_255_255/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.06)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black_40%,transparent_80%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -top-32 -left-32 -z-10 size-96 rounded-full bg-white/10 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -right-24 -bottom-40 -z-10 size-[28rem] rounded-full bg-sky-300/15 blur-3xl"></div>

    <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-4 py-10 sm:px-8 lg:grid-cols-4 lg:py-12">
        @foreach ($stats as $stat)
            <div class="group relative flex flex-col items-center overflow-hidden border border-white/15 bg-white/[0.06] px-4 py-5 text-center backdrop-blur-sm transition duration-300 hover:-translate-y-1 hover:border-white/30 hover:bg-white/10 sm:py-6">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-white transition-transform duration-300 group-hover:scale-x-100"></span>

                <span aria-hidden="true" class="mb-3 flex size-9 items-center justify-center border border-white/20 bg-white/10 text-white transition duration-300 group-hover:bg-white group-hover:text-brand">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $stat['icon'] !!}</svg>
                </span>

                <dt class="order-last mt-1 text-xs tracking-wide text-blue-100 uppercase sm:text-sm">{{ $stat['label'] }}</dt>
                <dd class="font-display text-4xl font-extrabold text-white sm:text-5xl">{{ $stat['value'] }}</dd>
                <span aria-hidden="true" class="order-last mt-2 h-px w-8 bg-white/40 transition-all duration-300 group-hover:w-12 group-hover:bg-white"></span>
            </div>
        @endforeach
    </dl>
</section>
