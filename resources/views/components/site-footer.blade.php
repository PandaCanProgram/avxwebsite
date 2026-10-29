@props([
    'logo' => null,
])

@php
    $navLinks = [
        ['label' => 'About', 'href' => route('home').'#about'],
        ['label' => 'Services', 'href' => route('home').'#services'],
        ['label' => 'Projects', 'href' => route('home').'#projects'],
        ['label' => 'Testimonials', 'href' => route('home').'#testimonials'],
        ['label' => 'FAQ', 'href' => route('home').'#faq'],
        ['label' => 'Contact', 'href' => route('home').'#contact'],
        ['label' => 'For Investors', 'href' => route('investment')],
    ];
@endphp

<footer class="bg-slate-800 text-gray-400">
    <div class="mx-auto max-w-7xl px-4 pt-12 pb-8 sm:px-8">
        <div class="grid gap-10 md:grid-cols-3">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="AVX Engineering Solutions" class="size-10 object-contain">
                    @endif

                    <span class="flex flex-col leading-none">
                        <span class="font-display text-xl font-extrabold tracking-[0.15em] text-white">AVX</span>
                        <span class="mt-1 text-[10px] font-medium tracking-[0.25em] text-gray-400 uppercase">Engineering Solutions</span>
                    </span>
                </a>

                <p class="mt-4 max-w-sm leading-relaxed">Drone fabrication, UAV systems, and software development — engineered for performance, reliability, and scalability.</p>
            </div>

            <div>
                <h2 class="font-display text-xs font-semibold tracking-[0.25em] text-gray-500 uppercase">Navigation</h2>

                <ul class="mt-4 grid w-fit grid-cols-2 gap-x-12 gap-y-2">
                    @foreach ($navLinks as $navLink)
                        <li>
                            <a href="{{ $navLink['href'] }}" class="text-gray-200 transition-colors hover:text-white">{{ $navLink['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="font-display text-xs font-semibold tracking-[0.25em] text-gray-500 uppercase">Contact</h2>

                <ul class="mt-4 space-y-3">
                    <li>
                        <a href="tel:+639349170567" class="inline-flex items-center gap-3 text-gray-200 transition-colors hover:text-white">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1Z" /></svg>
                            <span>0934 917 0567</span>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:info@avxengineering.com" class="inline-flex items-center gap-3 text-gray-200 transition-colors hover:text-white">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3" /><path d="m3 7 9 6 9-6" /></svg>
                            <span class="break-all">info@avxengineering.com</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/avxengineeringsolutions" target="_blank" rel="noopener" aria-label="Visit AVX Engineering Solutions on Facebook (opens in a new tab)" class="inline-flex items-center gap-3 text-gray-200 transition-colors hover:text-white">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-9h3l.5-4h-3.5V7c0-1 .3-1.5 1.7-1.5H17V2.1a24 24 0 0 0-2.6-.1C11.8 2 10 3.6 10 6.6V9H7v4h3v9h3.5Z" /></svg>
                            <span>AVX Engineering Solutions</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-slate-700 pt-6 text-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-gray-500">&copy; {{ date('Y') }} AVX Engineering Solutions. All rights reserved.</p>

            <p class="font-display text-xs tracking-[0.3em] text-slate-500 uppercase">Aviation &nbsp;|&nbsp; UAV &nbsp;|&nbsp; Systems</p>
        </div>
    </div>
</footer>
