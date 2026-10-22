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

    $sendOptions = [
        [
            'key' => 'viber',
            'label' => 'Viber',
            'description' => 'Opens our chat; your message is copied to paste',
            'color' => '#7360F2',
            'icon' => '<path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />',
        ],
        [
            'key' => 'gmail',
            'label' => 'Gmail',
            'description' => 'Opens an email with your message filled in',
            'color' => '#EA4335',
            'icon' => '<path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />',
        ],
        [
            'key' => 'facebook',
            'label' => 'Facebook Messenger',
            'description' => 'Opens our page chat; your message is copied to paste',
            'color' => '#0866FF',
            'icon' => '<path d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /><path d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />',
        ],
    ];

    $inputClasses = 'mt-2 block w-full border border-gray-200 bg-white px-4 py-3 text-slate-900 placeholder:text-gray-400 focus:border-brand focus:ring-1 focus:ring-brand focus:outline-none aria-[invalid=true]:border-red-500 aria-[invalid=true]:bg-red-50/40 aria-[invalid=true]:focus:border-red-500 aria-[invalid=true]:focus:ring-red-500';
    $labelClasses = 'font-display text-xs font-semibold tracking-[0.2em] text-gray-400 uppercase';
@endphp

<section id="contact" class="contact-scene scene-panel bg-slate-50" data-scene data-scene-label="Get in Touch">
    <div class="mx-auto grid max-w-7xl gap-16 px-4 py-16 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-20 lg:py-28">
        <div data-reveal>
            <p class="font-display text-sm font-semibold tracking-[0.35em] text-brand uppercase">Get in Touch</p>

            <h2 class="mt-6 font-display text-5xl leading-[1.05] font-extrabold text-slate-900 uppercase sm:text-6xl">Start Your Project Today</h2>

            <p class="mt-8 max-w-xl text-lg leading-relaxed text-gray-500">Have a project in mind? Whether it's your next unmanned system or your next digital solution, let's engineer it together. Consultation is unlimited.</p>

            <ul class="mt-10 space-y-6">
                @foreach ($contactDetails as $contactDetail)
                    <li class="flex items-center gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center bg-blue-100 text-brand">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $contactDetail['icon'] }}" />
                            </svg>
                        </span>

                        <div>
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

        <form id="quote" action="mailto:avxengineeringsolutions@gmail.com" method="POST" enctype="text/plain" class="bg-white p-6 shadow-sm sm:p-10" data-reveal data-quote-form data-validate>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="contact-name" class="{{ $labelClasses }}">Full Name</label>
                    <input id="contact-name" name="name" type="text" autocomplete="name" required data-required-message="Please enter your full name." placeholder="Juan dela Cruz" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="contact-email" class="{{ $labelClasses }}">Email Address</label>
                    <input id="contact-email" name="email" type="email" autocomplete="email" required data-required-message="Please enter your email address." placeholder="juan@email.com" class="{{ $inputClasses }}">
                </div>

                <div class="sm:col-span-2">
                    <label for="contact-subject" class="{{ $labelClasses }}">Subject</label>
                    <input id="contact-subject" name="subject" type="text" required data-required-message="Please enter a subject." placeholder="Custom Drone Fabrication" class="{{ $inputClasses }}">
                </div>

                <div class="sm:col-span-2">
                    <label for="contact-message" class="{{ $labelClasses }}">Message</label>
                    <textarea id="contact-message" name="message" rows="5" required data-required-message="Please tell us about your project." placeholder="Tell us about your project..." class="{{ $inputClasses }} resize-y"></textarea>
                </div>
            </div>

            <button type="submit" class="mt-6 w-full bg-brand px-6 py-3.5 font-semibold text-white transition-colors hover:bg-brand-dark">Send Message</button>
        </form>

        <dialog class="m-auto w-[calc(100%-2rem)] max-w-lg bg-white p-0 shadow-2xl backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm" aria-labelledby="send-via-title" data-send-via-dialog>
            <div class="p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-display text-xs font-semibold tracking-[0.3em] text-brand uppercase">Almost done</p>
                        <h3 id="send-via-title" class="mt-2 font-display text-3xl font-extrabold text-slate-900 uppercase">Where should we send it?</h3>
                    </div>

                    <button type="button" class="p-1 text-gray-400 transition-colors hover:text-slate-900" aria-label="Close" data-send-via-close>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="mt-3 text-gray-500">Pick an app. We'll open it with your message ready so you just hit send.</p>

                <div class="mt-6 grid gap-3">
                    @foreach ($sendOptions as $sendOption)
                        <a href="#" target="_blank" rel="noopener" class="group flex items-center gap-4 border border-gray-200 p-4 transition-colors hover:border-brand hover:bg-blue-50/50" data-send-via="{{ $sendOption['key'] }}">
                            <span class="flex size-11 shrink-0 items-center justify-center text-white" style="background-color: {{ $sendOption['color'] }}" aria-hidden="true">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $sendOption['icon'] !!}</svg>
                            </span>

                            <span class="flex-1">
                                <span class="block font-display text-lg font-bold tracking-wide text-slate-900 uppercase">{{ $sendOption['label'] }}</span>
                                <span class="block text-sm text-gray-500">{{ $sendOption['description'] }}</span>
                            </span>

                            <svg class="size-5 text-gray-300 transition-transform group-hover:translate-x-1 group-hover:text-brand" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    @endforeach
                </div>

                <p class="mt-4 hidden border-l-2 border-brand bg-blue-50 px-4 py-3 text-sm text-slate-900" role="status" data-send-via-status></p>

                <details class="mt-6">
                    <summary class="cursor-pointer font-display text-xs font-semibold tracking-[0.2em] text-gray-400 uppercase">Preview your message</summary>

                    <pre class="mt-3 max-h-48 overflow-auto bg-gray-50 p-4 font-sans text-sm whitespace-pre-wrap text-slate-700" data-send-via-preview></pre>

                    <button type="button" class="mt-3 font-medium text-brand hover:text-brand-dark" data-send-via-copy>Copy message</button>
                </details>
            </div>
        </dialog>
    </div>
</section>
