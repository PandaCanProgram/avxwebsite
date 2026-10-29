@props([
    'title' => null,
    'immersive' => false,
    'pageClass' => null,
    'description' => null,
])

@php
    $pageDescription = $description ?? config('seo.descriptions.'.(request()->route()?->getName() ?? 'home'), config('seo.descriptions.home'));
    $metaKeywords = collect(config('seo.keywords', []))->flatten()->unique()->implode(', ');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-20 motion-safe:scroll-smooth">
    <head>
        <meta charset="utf-8">
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
    <body @class(['bg-white font-sans antialiased', 'story-page' => $immersive, $pageClass]) data-investor-url="{{ route('investment') }}">
        @if ($immersive)
            <a href="#main-content" class="story-skip">Skip to content</a>
        @endif
        <x-site-header :logo="asset('avx-logo.webp')" />

        {{ $slot }}

        <x-site-footer :logo="asset('avx-logo.webp')" />

        <x-site-gallery />
    </body>
</html>
