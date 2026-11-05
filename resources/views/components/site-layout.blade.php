@props([
    'title' => null,
    'immersive' => false,
    'pageClass' => null,
    'description' => null,
])

@php
    $pageDescription = $description ?? config('seo.descriptions.'.(request()->route()?->getName() ?? 'home'), config('seo.descriptions.home'));
    $metaKeywords = collect(config('seo.keywords', []))->flatten()->unique()->implode(', ');
    $analyticsMeasurementId = config('services.google_analytics.measurement_id');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-20 motion-safe:scroll-smooth {{ config('inquiries.alpha_registration_enabled') ? 'has-alpha-registration' : '' }}">
    <head>
        <meta charset="utf-8">
        @if (app()->environment('production') && config('services.google_analytics.enabled') && is_string($analyticsMeasurementId) && preg_match('/\AG-[A-Z0-9]+\z/', $analyticsMeasurementId))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsMeasurementId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() { window.dataLayer.push(arguments); }
                gtag('js', new Date());
                gtag('config', {{ Illuminate\Support\Js::from($analyticsMeasurementId) }});
            </script>
        @endif
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if ($pageDescription)
            <meta name="description" content="{{ $pageDescription }}">
        @endif
        @if ($metaKeywords !== '')
            <meta name="keywords" content="{{ $metaKeywords }}">
        @endif

        <title>{{ $title ? $title.' | ' : '' }}AVX Engineering Solutions</title>

        <link rel="icon" type="image/png" href="{{ asset('avx-logo.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body @class(['site-body bg-white font-sans antialiased', 'story-page' => $immersive, $pageClass]) data-investor-url="{{ route('investment') }}">
        @if ($immersive)
            <a href="#main-content" class="story-skip">Skip to content</a>
        @endif
        <div class="site-navigation" data-site-navigation>
            <x-site-header :logo="asset('avx-logo.webp')" />

            @if (config('inquiries.alpha_registration_enabled'))
                <x-site-alpha-registration />
            @endif
        </div>

        {{ $slot }}

        <x-site-footer :logo="asset('avx-logo.webp')" />

        <x-site-gallery />
    </body>
</html>
