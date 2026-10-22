<x-site-layout :immersive="true">
    @if (! session('testimonialSubmitted') && ! $errors->getBag('testimonial')->any())
        <x-site-flight-intro />
    @endif

    <main id="main-content" tabindex="-1" data-story>
        <x-site-hero image="{{ Vite::asset('resources/images/AVXDrone.png') }}" />

        <x-site-scoring />

        <x-site-AboutUs :image="Vite::asset('resources/images/about-us.png')" />

        <x-site-services />

        <x-site-projects />

        <x-site-testimonials :testimonials="$testimonials" />

        <x-site-faq />

        <x-site-contact />
    </main>

    <x-site-story-controls />
</x-site-layout>
