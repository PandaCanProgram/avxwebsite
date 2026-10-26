<x-site-layout title="Invest in AVX" page-class="investment-page">
    <a href="#investment-main" class="investment-skip">Skip to content</a>

    <main id="investment-main" class="investment-main" tabindex="-1" data-investment-page>
        <div class="investment-atmosphere" aria-hidden="true">
            <div class="investment-grid"></div>
            <div class="investment-glow"></div>
        </div>

        <div class="investment-layout mx-auto max-w-7xl px-4 sm:px-8">
            <section class="investment-introduction" aria-labelledby="investment-title">
                <p class="investment-eyebrow" data-investment-reveal>Invest in AVX</p>
                <h1 id="investment-title" class="investment-title" data-investment-reveal>Build the future.<br><span>Be part of it.</span></h1>
                <p class="investment-description" data-investment-reveal>AVX Engineering Solutions brings ideas to life through custom UAV systems, software, and engineering design. We welcome conversations with individuals and organizations interested in investing in our next chapter.</p>

                <ul class="investment-focus" aria-label="AVX areas of work" data-investment-reveal>
                    <li>Drone & UAV</li>
                    <li>Software & AI</li>
                    <li>Engineering design</li>
                </ul>

                <div class="investment-visual" data-investment-reveal>
                    <div class="investment-visual-orbit" aria-hidden="true"></div>
                    <img src="{{ Vite::asset('resources/images/drone1.png') }}" alt="AVX F450 quadcopter" width="1024" height="1024" class="investment-drone" decoding="async">
                    <span class="investment-visual-caption" aria-hidden="true">From concept to flight.</span>
                </div>

                <a href="{{ route('home') }}#projects" class="investment-project-link" data-investment-reveal>Explore our work <span aria-hidden="true">&rarr;</span></a>
            </section>

            <div class="investment-inquiry" data-investment-reveal>
                <x-site-investor-form />
            </div>
        </div>
    </main>
</x-site-layout>
