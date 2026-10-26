@php
    $contactDetails = [
        [
            'label' => 'Email',
            'value' => 'avxengineeringsolutions@gmail.com',
            'href' => 'mailto:avxengineeringsolutions@gmail.com',
            'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
        ],
        [
            'label' => 'Phone / Viber',
            'value' => '+63 993 361 9061',
            'href' => 'tel:+639933619061',
            'icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z',
        ],
        [
            'label' => 'Location',
            'value' => 'Philippines — serving clients worldwide',
            'href' => null,
            'icon' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
        ],
    ];

    $inputClasses = 'mt-2 block w-full border border-gray-200 bg-white px-4 py-3 text-slate-900 placeholder:text-gray-400 focus:border-brand focus:ring-1 focus:ring-brand focus:outline-none aria-[invalid=true]:border-red-500 aria-[invalid=true]:bg-red-50/40 aria-[invalid=true]:focus:border-red-500 aria-[invalid=true]:focus:ring-red-500';
    $labelClasses = 'font-display text-xs font-semibold tracking-[0.2em] text-gray-400 uppercase';
@endphp

<section id="contact" class="contact-scene scene-panel bg-slate-50" data-scene data-scene-label="Get in Touch">
    <div class="engagement-atmosphere" aria-hidden="true">
        <div class="engagement-grid" data-parallax="0.06"></div>
        <div class="engagement-orbit contact-orbit" data-parallax="-0.1"><span></span><span></span></div>
    </div>
    <div class="mx-auto grid max-w-7xl gap-16 px-4 py-16 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-20 lg:py-28">
        <div data-reveal>
            <p class="scene-eyebrow font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Get in Touch</p>

            <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Start Your Project Today</h2>

            <p class="mt-8 max-w-xl text-lg leading-relaxed text-gray-500">Have a project in mind? Whether it's your next unmanned system or your next digital solution, let's engineer it together. Consultation is unlimited.</p>

            <ul class="contact-channels mt-10 space-y-6">
                @foreach ($contactDetails as $contactDetail)
                    <li class="flex items-center gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center bg-blue-100 text-brand">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $contactDetail['icon'] }}" />
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="font-display text-xs font-semibold tracking-[0.2em] text-gray-400 uppercase">{{ $contactDetail['label'] }}</p>

                            @if ($contactDetail['href'])
                                <a href="{{ $contactDetail['href'] }}" class="mt-0.5 block break-all text-slate-900 hover:text-brand">{{ $contactDetail['value'] }}</a>
                            @else
                                <p class="mt-0.5 text-slate-900">{{ $contactDetail['value'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <form id="quote" action="mailto:avxengineeringsolutions@gmail.com" method="POST" enctype="text/plain" class="contact-brief bg-white p-6 shadow-sm sm:p-10" data-reveal="right" data-quote-form data-validate>
            <div class="contact-brief-heading">
                <p class="font-display text-xs font-semibold tracking-[0.25em] uppercase">Your project brief</p>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6" /></svg>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="contact-name" class="{{ $labelClasses }}">Full Name</label>
                    <input id="contact-name" name="name" type="text" autocomplete="name" required data-required-message="Please enter your full name." placeholder="Juan dela Cruz" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="contact-email" class="{{ $labelClasses }}">Email Address</label>
                    <input id="contact-email" name="email" type="email" autocomplete="email" required data-required-message="Please enter your email address." placeholder="juan@email.com" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="contact-organization" class="{{ $labelClasses }}">Company / Organization <span class="normal-case tracking-normal">(optional)</span></label>
                    <input id="contact-organization" name="organization" type="text" autocomplete="organization" maxlength="150" placeholder="Company or organization" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="contact-subject" class="{{ $labelClasses }}">Subject</label>
                    <input id="contact-subject" name="subject" type="text" required data-required-message="Please enter a subject." placeholder="Custom Drone Fabrication" class="{{ $inputClasses }}">
                </div>

                <div class="sm:col-span-2">
                    <label for="contact-message" class="{{ $labelClasses }}">Message</label>
                    <textarea id="contact-message" name="message" rows="5" required data-required-message="Please tell us about your project." placeholder="Tell us about your project..." class="{{ $inputClasses }} resize-y"></textarea>
                </div>
            </div>

            <button type="submit" class="contact-send mt-6 w-full bg-brand px-6 py-3.5 font-semibold text-white transition-colors hover:bg-brand-dark">
                <span>Send Message</span>
                <span aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19 19 5M5 5h14v14" /></svg>
                </span>
            </button>
        </form>

        <x-site-send-options />
    </div>
</section>
