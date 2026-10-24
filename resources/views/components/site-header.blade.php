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
        <a href="{{ route('home') }}#intro" class="flex items-center gap-3" data-home-link>
            @if ($logo)
                <img src="{{ $logo }}" alt="AVX Engineering Solutions" class="size-12 translate-y-0.5">
            @endif

            <span class="flex flex-col leading-none">
                <span class="font-display text-3xl font-extrabold tracking-[0.15em] text-slate-900">AVX</span>
                <span class="mt-1 text-[11px] font-medium tracking-[0.25em] text-gray-400 uppercase">Engineering Solutions</span>
            </span>
        </a>

        <nav class="hidden items-center gap-9 lg:flex" aria-label="Main">
            @foreach ($navLinks as $navLink)
                <a href="{{ $navLink['href'] }}" class="group relative py-2 font-display text-lg font-semibold tracking-[0.15em] text-slate-600 uppercase transition-colors hover:text-slate-900 aria-[current=true]:text-brand" data-nav-link>
                    {{ $navLink['label'] }}
                    <span class="absolute inset-x-0 bottom-0 h-0.5 origin-left scale-x-0 bg-brand transition-transform duration-300 group-hover:scale-x-100 group-aria-[current=true]:scale-x-100" aria-hidden="true"></span>
                </a>
            @endforeach

            <a href="{{ route('home') }}#quote" class="group ml-3 inline-flex items-center gap-2 bg-brand px-6 py-3 font-display text-lg font-semibold tracking-[0.15em] text-white uppercase transition-colors hover:bg-brand-dark">
                Get a Quote
                <svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </nav>

        <button type="button" class="p-2 text-slate-900 lg:hidden" aria-controls="mobile-nav" aria-expanded="false" aria-label="Toggle navigation" data-nav-toggle>
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    <nav id="mobile-nav" class="hidden border-t border-gray-100 px-4 pb-4 lg:hidden" aria-label="Mobile">
        @foreach ($navLinks as $navLink)
            <a href="{{ $navLink['href'] }}" class="block border-l-2 border-transparent py-3 pl-3 font-display text-lg font-semibold tracking-[0.15em] text-slate-600 uppercase transition-colors hover:text-slate-900 aria-[current=true]:border-brand aria-[current=true]:text-brand" data-nav-link>{{ $navLink['label'] }}</a>
        @endforeach

        <a href="{{ route('home') }}#quote" class="mt-2 block bg-brand px-6 py-3 text-center font-display text-lg font-semibold tracking-[0.15em] text-white uppercase hover:bg-brand-dark">Get a Quote</a>
    </nav>

</header>
