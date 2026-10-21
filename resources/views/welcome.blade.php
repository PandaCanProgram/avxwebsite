<x-site-layout>
    <main>
        <x-site-hero image="{{ Vite::asset('resources/images/AVXDrone.png') }}" />
    </main>

    <x-site-scoring />

    <x-site-AboutUs :image="Vite::asset('resources/images/about-us.png')" />

    <x-site-services />

    <x-site-projects />

    <x-site-testimonials :testimonials="$testimonials" />

    <x-site-faq />

    <x-site-contact />
</x-site-layout>
