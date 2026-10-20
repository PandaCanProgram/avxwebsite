@php
    $faqs = [
        [
            'question' => 'What kinds of drones can you build?',
            'answer' => 'Both multirotor and fixed-wing UAVs. Multirotors are stable and versatile for aerial photography, inspection, mapping, surveillance, and research. Fixed-wing platforms offer long range and high endurance for surveying, monitoring, agriculture, and delivery. Our portfolio includes the F450 and F500 platforms.',
        ],
        [
            'question' => 'Can you set up the autopilot and payload too?',
            'answer' => 'Yes. Beyond fabrication, we handle system integration and autopilot setup, payload and mission planning, flight performance analysis, and prototype testing and validation, so your drone is ready for its mission.',
        ],
        [
            'question' => 'What kind of software do you build?',
            'answer' => 'Business websites, management systems, AI applications, UAV and aviation software, research platforms, industrial automation, educational systems, and custom web applications. We work with HTML, CSS, Tailwind CSS, JavaScript, TypeScript, React, Next.js, FastAPI, and Figma, plus Python and PyTorch for AI and machine learning.',
        ],
        [
            'question' => 'Is consultation limited?',
            'answer' => 'No. Consultation is unlimited, so you can talk your project through with us as much as you need before and during development.',
        ],
        [
            'question' => 'How long does a typical project take?',
            'answer' => 'It depends on scope. We review your requirements first and agree on a timeline before any work begins.',
        ],
        [
            'question' => 'How do I get a quote?',
            'answer' => 'Send us a short description of your project, deliverables, and deadline through the form below, by email, or by phone. We\'ll review the requirements and reply with a scope, timeline, and quote.',
        ],
    ];
@endphp

<section id="faq" class="bg-white">
    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-8 lg:py-28">
        <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Common Questions</p>

        <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">FAQ</h2>

        <div class="mt-16 divide-y divide-gray-100">
            @foreach ($faqs as $faq)
                <details class="group py-6" @if ($loop->index === 1) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 [&::-webkit-details-marker]:hidden">
                        <h3 class="font-display text-lg font-bold text-slate-900 uppercase">{{ $faq['question'] }}</h3>

                        <svg class="size-5 shrink-0 text-brand transition-transform duration-200 group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </summary>

                    <p class="mt-4 pr-10 text-lg leading-relaxed text-gray-400">{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
