@props([
    'logo' => null,
])

@php
    $navLinks = [
        ['label' => 'About', 'href' => route('home').'#about'],
        ['label' => 'Services', 'href' => route('home').'#services'],
        ['label' => 'Projects', 'href' => route('home').'#projects'],
        ['label' => 'Contact', 'href' => route('home').'#contact'],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-gray-100 bg-white">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-4 sm:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            @if ($logo)
                <img src="{{ $logo }}" alt="AVX Engineering Solutions" class="size-12 translate-y-0.5">
            @endif

            <span class="flex flex-col leading-none">
                <span class="text-2xl font-extrabold tracking-[0.2em] text-slate-900">AVX</span>
                <span class="mt-1 text-[11px] font-medium tracking-[0.25em] text-gray-400 uppercase">Engineering Solutions</span>
            </span>
        </a>

        <nav class="hidden items-center gap-10 md:flex" aria-label="Main">
            @foreach ($navLinks as $navLink)
                <a href="{{ $navLink['href'] }}" class="text-[17px] text-gray-500 transition-colors hover:text-slate-900">{{ $navLink['label'] }}</a>
            @endforeach

            <a href="{{ route('home') }}#quote" class="ml-2 bg-brand px-6 py-3 text-[17px] font-medium text-white transition-colors hover:bg-brand-dark">Get a Quote</a>
        </nav>

        <button type="button" class="p-2 text-slate-900 md:hidden" aria-controls="mobile-nav" aria-expanded="false" aria-label="Toggle navigation" data-nav-toggle>
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    <nav id="mobile-nav" class="hidden border-t border-gray-100 px-4 pb-4 md:hidden" aria-label="Mobile">
        @foreach ($navLinks as $navLink)
            <a href="{{ $navLink['href'] }}" class="block py-3 text-gray-500 hover:text-slate-900">{{ $navLink['label'] }}</a>
        @endforeach

        <a href="{{ route('home') }}#quote" class="mt-2 block bg-brand px-6 py-3 text-center font-medium text-white hover:bg-brand-dark">Get a Quote</a>
    </nav>

</header>
