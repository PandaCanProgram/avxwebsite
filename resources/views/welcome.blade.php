<x-site-layout :immersive="true">
    @if (! session('testimonialSubmitted') && ! $errors->getBag('testimonial')->any())
        <x-site-flight-intro />
    @endif

    <main id="main-content" tabindex="-1" data-story>
        <x-site-hero image="{{ Vite::asset('resources/images/AVXDrone.png') }}" />

        <x-site-AboutUs :image="asset('avx-logo.png')" />

        <x-site-services />

        <x-site-projects />

        <x-site-testimonials :testimonials="$testimonials" />

        <x-site-faq />

        <x-site-contact />

        <x-site-investment />
    </main>

    <x-site-story-controls />
</x-site-layout>
