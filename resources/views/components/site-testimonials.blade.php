@props([
    'testimonials' => collect(),
])

@php
    $starPath = 'M12 2.5l2.94 5.96 6.56.95-4.75 4.63 1.12 6.54L12 17.5l-5.87 3.08 1.12-6.54L2.5 9.41l6.56-.95L12 2.5z';
@endphp

<section id="testimonials" class="testimonials-scene scene-panel bg-blue-50" data-scene data-scene-label="Client Feedback">
    <div class="engagement-atmosphere" aria-hidden="true">
        <div class="engagement-grid" data-parallax="0.06"></div>
        <div class="engagement-orbit feedback-orbit" data-parallax="-0.12"><span></span><span></span></div>
        <div class="engagement-watermark" data-parallax="0.1">AVX</div>
    </div>
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <div data-reveal class="text-center">
            <p class="engagement-kicker font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Client Feedback</p>

            <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">What They Say</h2>
        </div>



        @if ($testimonials->isEmpty())
            <p class="mx-auto mt-16 max-w-xl text-center text-lg text-gray-500">Client feedback will be shared here soon.</p>
        @else
            <div class="feedback-carousel relative mx-auto mt-16 max-w-4xl" data-reveal data-carousel aria-roledescription="carousel" aria-label="Client feedback">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-out motion-reduce:transition-none" data-carousel-track>
                        @foreach ($testimonials as $testimonial)
                            <figure class="w-full shrink-0 px-1 sm:px-16" data-carousel-slide aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $loop->count }}">
                                <div class="feedback-card relative h-full border border-blue-100 bg-white px-8 py-12 text-center sm:px-14 sm:py-14">
                                    <span class="feedback-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($loop->count, 2, '0', STR_PAD_LEFT) }}</span>
                                    <svg class="absolute top-6 left-6 size-10 text-brand/10 sm:size-14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M9.5 6C6.46 6 4 8.46 4 11.5V18h6v-6H6.5A3 3 0 0 1 9.5 9V6Zm10 0C16.46 6 14 8.46 14 11.5V18h6v-6h-3.5a3 3 0 0 1 3-3V6Z" />
                                    </svg>

                                    <div class="flex justify-center gap-1" role="img" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <svg class="size-5 {{ $star <= $testimonial->rating ? 'text-brand' : 'text-blue-100' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="{{ $starPath }}" />
                                            </svg>
                                        @endfor
                                    </div>

                                    <blockquote class="feedback-quote mt-8 text-xl leading-relaxed whitespace-pre-line text-gray-500 italic sm:text-2xl">"{{ $testimonial->message }}"</blockquote>

                                    <figcaption class="feedback-author mt-10">
                                        <span class="mx-auto mb-5 block h-0.5 w-10 bg-brand" aria-hidden="true"></span>
                                        <p class="font-display text-xl font-bold text-slate-900 uppercase">{{ $testimonial->name }}</p>

                                        @if ($testimonial->role)
                                            <p class="mt-1 text-gray-400">{{ $testimonial->role }}</p>
                                        @endif
                                    </figcaption>
                                </div>
                            </figure>
                        @endforeach
                    </div>
                </div>

                @if ($testimonials->count() > 1)
                    <button type="button" class="absolute top-1/2 left-0 hidden size-11 -translate-y-1/2 items-center justify-center border border-blue-100 bg-white text-brand transition-colors hover:bg-brand hover:text-white sm:flex" data-carousel-prev aria-label="Previous feedback">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <button type="button" class="absolute top-1/2 right-0 hidden size-11 -translate-y-1/2 items-center justify-center border border-blue-100 bg-white text-brand transition-colors hover:bg-brand hover:text-white sm:flex" data-carousel-next aria-label="Next feedback">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>

                    <div class="feedback-controls mt-8 flex items-center justify-center gap-2">
                        @foreach ($testimonials as $testimonial)
                            <button type="button" class="h-2 w-2 bg-brand/25 transition-all duration-300 hover:bg-brand/50 aria-[current=true]:w-8 aria-[current=true]:bg-brand" data-carousel-dot aria-label="Show feedback {{ $loop->iteration }}" @if ($loop->first) aria-current="true" @endif></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif


    </div>
</section>
