@php
    $faqs = [
        [
            'question' => 'What simulation tools do you use?',
            'answer' => 'We run CFD in ANSYS Fluent and OpenFOAM, structural FEA in ANSYS Mechanical, and build CAD models in SolidWorks and CATIA — the same industry-standard tools used in academic and professional aerospace work.',
        ],
        [
            'question' => 'Can you help with thesis or capstone projects?',
            'answer' => 'Absolutely. We provide full technical consultation for undergraduate and graduate theses — from simulation setup to data analysis, report writing, and panel preparation.',
        ],
        [
            'question' => 'How long does a typical project take?',
            'answer' => 'It depends on scope. A focused simulation or CAD task usually takes one to two weeks, while full UAV builds or multi-stage research support can run several weeks. We agree on a timeline before any work begins.',
        ],
        [
            'question' => 'Do you work with international clients?',
            'answer' => 'Yes. We work remotely with clients worldwide and coordinate through online meetings, shared files, and regular progress updates across time zones.',
        ],
        [
            'question' => 'How do I get a quote?',
            'answer' => 'Send us a short description of your project, deliverables, and deadline. We\'ll review the requirements and reply with a scope, timeline, and quote — free and with no obligation.',
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
