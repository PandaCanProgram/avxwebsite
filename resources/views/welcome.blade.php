<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} AVX</title>

        <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/AVXblack.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white font-sans antialiased">
        <x-site-header :logo="Vite::asset('resources/images/AVXblack.png')" />

        <main>
            <x-site-hero image="{{ Vite::asset('resources/images/AVXDrone.png') }}" />
        </main>

        <x-site-scoring />

        <x-site-AboutUs />

        <x-site-services />

        
    </body>
</html>
