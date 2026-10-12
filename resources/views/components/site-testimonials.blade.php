@php
    $testimonials = [
        [
            'quote' => 'AVX helped me finish my thesis simulation on time. Their CFD analysis was accurate, well-documented, and matched our panel\'s expectations exactly.',
            'name' => 'Renz M.',
            'role' => 'BS Aerospace Engineering Student',
            'rating' => 5,
        ],
        [
            'quote' => 'They built me a custom quadcopter with full FPV setup. Fast turnaround, clear communication, and the drone performs better than expected.',
            'name' => 'Carlo S.',
            'role' => 'RC Enthusiast & Client',
            'rating' => 5,
        ],
        [
            'quote' => 'The FEA report was detailed, professional, and precisely what our graduate committee required. I couldn\'t have passed without their support.',
            'name' => 'Janine R.',
            'role' => 'Graduate Researcher',
            'rating' => 5,
        ],
    ];
@endphp

<section id="testimonials" class="bg-blue-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Client Feedback</p>

        <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">What They Say</h2>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <figure class="border border-blue-100 bg-white p-8 sm:p-10">
                    <div class="flex gap-1 text-brand" aria-label="{{ $testimonial['rating'] }} out of 5 stars">
                        @for ($star = 0; $star < $testimonial['rating']; $star++)
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2.5l2.94 5.96 6.56.95-4.75 4.63 1.12 6.54L12 17.5l-5.87 3.08 1.12-6.54L2.5 9.41l6.56-.95L12 2.5z" />
                            </svg>
                        @endfor
                    </div>

                    <blockquote class="mt-6 text-lg leading-relaxed text-gray-500 italic">"{{ $testimonial['quote'] }}"</blockquote>

                    <figcaption class="mt-8">
                        <p class="font-display text-lg font-bold text-slate-900 uppercase">{{ $testimonial['name'] }}</p>
                        <p class="mt-1 text-sm text-gray-400">{{ $testimonial['role'] }}</p>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
