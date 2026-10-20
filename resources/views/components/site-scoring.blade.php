@props([
    'strengths' => [
        [
            'title' => 'Unli Consultation',
            'description' => 'Talk your project through with us as often as you need.',
            'icon' => '<path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7l-4 4v-4H6a2 2 0 0 1-2-2z" /><path d="M8 8.5h8M8 11.5h5" />',
        ],
        [
            'title' => 'Budget Friendly',
            'description' => 'Cost-effective builds without cutting corners.',
            'icon' => '<circle cx="12" cy="12" r="8.5" /><path d="M14.5 9.25c-.4-.9-1.35-1.5-2.5-1.5-1.5 0-2.5.85-2.5 2s1 1.65 2.5 2 2.5.85 2.5 2-1 2-2.5 2c-1.15 0-2.1-.6-2.5-1.5M12 6.5v1.25m0 8.5v1.25" />',
        ],
        [
            'title' => 'High Quality Output',
            'description' => 'Built for performance, reliability, and scalability.',
            'icon' => '<circle cx="12" cy="9" r="5.5" /><path d="m9.75 9 1.5 1.5 3-3" /><path d="m8.5 13.5-1.5 7 5-2.5 5 2.5-1.5-7" />',
        ],
        [
            'title' => 'Professional Team',
            'description' => 'Engineers and developers across aviation and software.',
            'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />',
        ],
    ],
])

<section class="relative isolate overflow-hidden bg-gradient-to-br from-brand to-brand-dark">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgb(255_255_255/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.06)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black_40%,transparent_80%)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -top-32 -left-32 -z-10 size-96 rounded-full bg-white/10 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -right-24 -bottom-40 -z-10 size-[28rem] rounded-full bg-sky-300/15 blur-3xl"></div>

    <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-4 py-10 sm:px-8 lg:grid-cols-4 lg:py-12">
        @foreach ($strengths as $strength)
            <div class="group relative flex flex-col items-center overflow-hidden border border-white/15 bg-white/[0.06] px-4 py-5 text-center backdrop-blur-sm transition duration-300 hover:-translate-y-1 hover:border-white/30 hover:bg-white/10 sm:py-6">
                <span aria-hidden="true" class="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-white transition-transform duration-300 group-hover:scale-x-100"></span>

                <span aria-hidden="true" class="mb-3 flex size-9 items-center justify-center border border-white/20 bg-white/10 text-white transition duration-300 group-hover:bg-white group-hover:text-brand">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $strength['icon'] !!}</svg>
                </span>

                <dt class="font-display text-xl font-extrabold text-white uppercase sm:text-2xl">{{ $strength['title'] }}</dt>
                <dd class="mt-1 text-xs text-blue-100 sm:text-sm">{{ $strength['description'] }}</dd>
                <span aria-hidden="true" class="mt-3 h-px w-8 bg-white/40 transition-all duration-300 group-hover:w-12 group-hover:bg-white"></span>
            </div>
        @endforeach
    </dl>
</section>
