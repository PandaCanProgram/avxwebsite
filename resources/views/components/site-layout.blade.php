@props([
    'title' => null,
    'immersive' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-20 motion-safe:scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name', 'Laravel') }} AVX</title>

        <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/AVXblack.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body @class(['bg-white font-sans antialiased', 'story-page' => $immersive])>
        @if ($immersive)
            <a href="#main-content" class="story-skip">Skip to content</a>
        @endif
        <x-site-header :logo="Vite::asset('resources/images/AVXblack.png')" />

        {{ $slot }}

        <x-site-footer :logo="Vite::asset('resources/images/AVXlogo.png')" />

        <x-site-gallery />
    </body>
</html>
