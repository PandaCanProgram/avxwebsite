@props([
    'logo' => null,
])

@php
    $navLinks = [
        ['label' => 'About', 'href' => '#about'],
        ['label' => 'Services', 'href' => '#services'],
        ['label' => 'Projects', 'href' => '#projects'],
        ['label' => 'Testimonials', 'href' => '#testimonials'],
        ['label' => 'FAQ', 'href' => '#faq'],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
@endphp

<footer class="bg-slate-800 text-gray-400">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8">
        <div class="grid gap-12 md:grid-cols-3">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="AVX Engineering Solutions" class="size-10">
                    @endif

                    <span class="flex flex-col leading-none">
                        <span class="font-display text-xl font-extrabold tracking-[0.15em] text-white">AVX</span>
                        <span class="mt-1 text-[10px] font-medium tracking-[0.25em] text-blue-200/70 uppercase">Engineering Solutions</span>
                    </span>
                </a>

                <p class="mt-6 max-w-sm leading-relaxed">Delivering cost-effective engineering solutions for the next generation of aerospace innovation.</p>
            </div>

            <div>
                <h2 class="font-display text-xs font-semibold tracking-[0.25em] text-gray-500 uppercase">Navigation</h2>

                <ul class="mt-6 space-y-3">
                    @foreach ($navLinks as $navLink)
                        <li>
                            <a href="{{ $navLink['href'] }}" class="text-gray-200 transition-colors hover:text-white">{{ $navLink['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="font-display text-xs font-semibold tracking-[0.25em] text-gray-500 uppercase">Contact</h2>

                <ul class="mt-6 space-y-3">
                    <li><a href="mailto:avxengineeringsolutions@gmail.com" class="break-all text-gray-200 transition-colors hover:text-white">avxengineeringsolutions@gmail.com</a></li>
                    <li><a href="tel:09933619061" class="text-gray-200 transition-colors hover:text-white">09933619061</a></li>
                    <li>Philippines — worldwide</li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-slate-700 pt-8 text-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-gray-500">&copy; {{ date('Y') }} AVX Engineering Solutions. All rights reserved.</p>

            <p class="font-display text-xs tracking-[0.3em] text-slate-500 uppercase">Aviation &nbsp;|&nbsp; UAV &nbsp;|&nbsp; Systems</p>
        </div>
    </div>
</footer>
