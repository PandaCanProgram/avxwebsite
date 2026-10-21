@props([
    'testimonials' => collect(),
])

@php
    $feedbackErrors = $errors->getBag('testimonial');
    $inputClasses = 'mt-2 block w-full border border-gray-200 bg-white px-4 py-3 text-slate-900 placeholder:text-gray-400 focus:border-brand focus:ring-1 focus:ring-brand focus:outline-none aria-[invalid=true]:border-red-500 aria-[invalid=true]:bg-red-50/40 aria-[invalid=true]:focus:border-red-500 aria-[invalid=true]:focus:ring-red-500';
    $labelClasses = 'font-display text-xs font-semibold tracking-[0.2em] text-gray-400 uppercase';
    $starPath = 'M12 2.5l2.94 5.96 6.56.95-4.75 4.63 1.12 6.54L12 17.5l-5.87 3.08 1.12-6.54L2.5 9.41l6.56-.95L12 2.5z';
@endphp

<section id="testimonials" class="bg-blue-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-28">
        <div class="text-center">
            <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Client Feedback</p>

            <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">What They Say</h2>
        </div>

        @if (session('testimonialSubmitted'))
            <p class="mx-auto mt-10 max-w-3xl border-l-2 border-brand bg-white px-5 py-4 text-slate-900" role="status">{{ session('testimonialSubmitted') }}</p>
        @endif

        @if ($testimonials->isEmpty())
            <p class="mx-auto mt-16 max-w-xl text-center text-lg text-gray-500">No feedback yet. Worked with us? Be the first to share your experience.</p>
        @else
            <div class="relative mx-auto mt-16 max-w-4xl" data-carousel aria-roledescription="carousel" aria-label="Client feedback">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-out motion-reduce:transition-none" data-carousel-track>
                        @foreach ($testimonials as $testimonial)
                            <figure class="w-full shrink-0 px-1 sm:px-16" data-carousel-slide aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $loop->count }}">
                                <div class="relative h-full border border-blue-100 bg-white px-8 py-12 text-center sm:px-14 sm:py-14">
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

                                    <blockquote class="mt-8 text-xl leading-relaxed whitespace-pre-line text-gray-500 italic sm:text-2xl">"{{ $testimonial->message }}"</blockquote>

                                    <figcaption class="mt-10">
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

                    <div class="mt-8 flex justify-center gap-2">
                        @foreach ($testimonials as $testimonial)
                            <button type="button" class="h-2 w-2 bg-brand/25 transition-all duration-300 hover:bg-brand/50 aria-[current=true]:w-8 aria-[current=true]:bg-brand" data-carousel-dot aria-label="Show feedback {{ $loop->iteration }}" @if ($loop->first) aria-current="true" @endif></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <details class="group mt-12 text-center" data-feedback-form @if ($feedbackErrors->any()) open @endif>
            <summary class="inline-flex cursor-pointer list-none items-center gap-3 bg-brand px-8 py-3.5 font-medium text-white transition-colors hover:bg-brand-dark [&::-webkit-details-marker]:hidden">
                <svg class="size-5 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                <span class="group-open:hidden">Add Your Feedback</span>
                <span class="hidden group-open:inline">Close</span>
            </summary>

            <form action="{{ route('testimonials.store') }}" method="POST" class="mx-auto mt-6 max-w-3xl bg-white p-6 text-left shadow-sm sm:p-10" data-validate>
                @csrf

                <div class="hidden" aria-hidden="true">
                    <label for="feedback-website">Leave this field empty</label>
                    <input id="feedback-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="feedback-name" class="{{ $labelClasses }}">Your Name</label>
                        <input id="feedback-name" name="name" type="text" autocomplete="name" required maxlength="100" data-required-message="Please enter your name." value="{{ old('name') }}" @if ($feedbackErrors->has('name')) aria-invalid="true" @endif placeholder="Juan D." class="{{ $inputClasses }}">
                        @if ($feedbackErrors->has('name'))
                            <p class="mt-2 text-sm text-red-600" data-field-error="name">{{ $feedbackErrors->first('name') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="feedback-role" class="{{ $labelClasses }}">Role or Company <span class="normal-case tracking-normal">(optional)</span></label>
                        <input id="feedback-role" name="role" type="text" autocomplete="organization-title" maxlength="100" value="{{ old('role') }}" @if ($feedbackErrors->has('role')) aria-invalid="true" @endif placeholder="Drone Client" class="{{ $inputClasses }}">
                        @if ($feedbackErrors->has('role'))
                            <p class="mt-2 text-sm text-red-600" data-field-error="role">{{ $feedbackErrors->first('role') }}</p>
                        @endif
                    </div>

                    <fieldset class="sm:col-span-2">
                        <legend class="{{ $labelClasses }}">Rating</legend>

                        <div class="mt-2 flex w-fit flex-row-reverse justify-end gap-1">
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <input id="feedback-rating-{{ $rating }}" name="rating" type="radio" value="{{ $rating }}" required data-required-message="Please choose a star rating." class="peer sr-only" @checked((int) old('rating') === $rating)>
                                <label for="feedback-rating-{{ $rating }}" class="cursor-pointer text-gray-300 transition-colors peer-checked:text-brand peer-focus-visible:outline-2 peer-focus-visible:outline-brand">
                                    <span class="sr-only">{{ $rating }} {{ Str::plural('star', $rating) }}</span>
                                    <svg class="size-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="{{ $starPath }}" />
                                    </svg>
                                </label>
                            @endfor
                        </div>

                        @if ($feedbackErrors->has('rating'))
                            <p class="mt-2 text-sm text-red-600" data-field-error="rating">{{ $feedbackErrors->first('rating') }}</p>
                        @endif
                    </fieldset>

                    <div class="sm:col-span-2">
                        <label for="feedback-message" class="{{ $labelClasses }}">Your Feedback</label>
                        <textarea id="feedback-message" name="message" rows="4" required minlength="10" maxlength="1000" data-required-message="Please write your feedback." @if ($feedbackErrors->has('message')) aria-invalid="true" @endif placeholder="How was working with AVX?" class="{{ $inputClasses }} resize-y">{{ old('message') }}</textarea>
                        @if ($feedbackErrors->has('message'))
                            <p class="mt-2 text-sm text-red-600" data-field-error="message">{{ $feedbackErrors->first('message') }}</p>
                        @endif
                    </div>
                </div>

                @if ($feedbackErrors->has('website'))
                    <p class="mt-4 text-sm text-red-600">{{ $feedbackErrors->first('website') }}</p>
                @endif

                <button type="submit" class="mt-6 w-full bg-brand px-6 py-3.5 font-semibold text-white transition-colors hover:bg-brand-dark">Submit Feedback</button>
            </form>
        </details>
    </div>
</section>
