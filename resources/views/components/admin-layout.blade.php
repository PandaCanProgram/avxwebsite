@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-admin-theme="{{ auth()->user()?->admin_theme ?? 'light' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>{{ $title }} | AVX Administration</title>
        <link rel="icon" type="image/png" href="{{ asset('avx-logo.png') }}">
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/admin.js'])
        @endif
    </head>
    <body class="admin-body font-sans">
        <a class="admin-skip" href="#main-content">Skip to content</a>
        {{ $slot }}
    </body>
</html>
