@php
    $packages = collect(config('drone-packages.packages'));
    $selectedPackage = $packages->firstWhere('id', request()->query('package'));
    $groups = [
        ['id' => 'field', 'number' => '01', 'label' => 'Lifting & spraying', 'title' => 'Put your payload to work.', 'description' => 'Choose a payload class, then discuss the lifting or spraying configuration your application needs.'],
        ['id' => 'development', 'number' => '02', 'label' => 'Research & development', 'title' => 'Start with a platform. Build your idea.', 'description' => 'Explore a development kit for your next research, integration, or prototype project.'],
    ];
    $buildScope = [
        ['title' => 'Drone build', 'detail' => 'Platform & configuration', 'path' => 'M6 6l12 12M6 18 18 6M3 6h6M15 6h6M3 18h6m6 0h6M10 10h4v4h-4z'],
        ['title' => 'RC equipment', 'detail' => 'Control setup', 'path' => 'M6 7 4 3m14 4 2-4M5 8h14l2 12H3L5 8Zm2 5h4m-2-2v4m7-3v.1m2 2v.1'],
        ['title' => 'FPV equipment', 'detail' => 'Viewing setup', 'path' => 'M3 8h18v10h-6l-3-3-3 3H3V8Zm4 3v3m10-3v3M7 8V5h10v3'],
        ['title' => 'Charger', 'detail' => 'Charging equipment', 'path' => 'M6 8h12v13H6V8Zm3 0V3m6 5V3m-3 8-2 4h4l-2 4'],
        ['title' => '2 batteries', 'detail' => 'Battery configuration', 'path' => 'M3 7h8v14H3V7Zm10 0h8v14h-8V7ZM5 3h4v4H5V3Zm10 0h4v4h-4V3Z'],
    ];
@endphp

<x-site-layout title="Drone Packages & Pricing" page-class="portfolio-page drone-packages-page">
    <a href="#drone-packages-main" class="portfolio-skip">Skip to drone packages</a>
    <main id="drone-packages-main" class="drone-packages-main" tabindex="-1" data-drone-packages>
        <section class="packages-hero package-shell" aria-labelledby="packages-title">
            <div class="packages-hero-copy" data-package-reveal>
                <p class="package-eyebrow">AVX / Drone systems & pricing</p>
                <h1 id="packages-title">Your mission.<br><span>Your next drone.</span></h1>
                <p class="package-lead">From lifting and spraying to your next development project. Find a drone package that fits your direction, with pricing you can plan around.</p>
                <div class="packages-hero-actions"><a class="package-button" href="#package-options">Explore packages <span aria-hidden="true">↓</span></a><a class="package-text-link" href="#package-comparison">Compare options <span aria-hidden="true">↗</span></a></div>
                <p class="packages-price-note">Indicative prices in Philippine pesos. Final configuration, taxes, and delivery are confirmed in your quotation.</p>
            </div>
            <div class="packages-hero-console" data-package-reveal>
                <div class="package-console-heading"><span>ENGINEERED AROUND YOUR APPLICATION</span><span aria-hidden="true">AVX / UAV</span></div>
                <div class="package-hero-diagram"><x-drone-package-diagram /><span class="package-hero-word" aria-hidden="true">UAV</span></div>
                <div class="packages-hero-specs"><div><strong>10–20<span>kg</span></strong><span>Payload options</span></div><div><strong>₱140<span>k</span></strong><span>Development kits from</span></div><div><strong>04</strong><span>Package options</span></div></div>
                <p class="package-console-caption">LIFTER / SPRAYER / DEVELOPMENT KITS <span>Platform illustration</span></p>
            </div>
        </section>

        <nav class="packages-section-nav package-shell" aria-label="Drone package sections"><a href="#field"><span>01</span> Lifter & sprayer packages</a><a href="#development"><span>02</span> Development kits</a><a href="#package-comparison"><span>03</span> Compare & choose</a></nav>

        <section id="package-options" class="packages-catalog package-shell" aria-label="Drone package options">
            @foreach ($groups as $group)
                <section id="{{ $group['id'] }}" class="packages-group" aria-labelledby="{{ $group['id'] }}-title">
                    <div class="packages-group-heading" data-package-reveal><div><p class="package-eyebrow">{{ $group['number'] }} / {{ $group['label'] }}</p><h2 id="{{ $group['id'] }}-title">{{ $group['title'] }}</h2></div><p>{{ $group['description'] }}</p></div>
                    <div class="packages-card-grid">
                        @foreach ($packages->where('group', $group['id']) as $package)
                            <x-drone-package-card :package="$package" :number="$packages->search($package) + 1" />
                        @endforeach
                    </div>
                </section>
            @endforeach
        </section>

        <section class="packages-scope-section" aria-labelledby="package-scope-title">
            <div class="package-shell">
                <div class="packages-group-heading" data-package-reveal><div><p class="package-eyebrow">The build conversation</p><h2 id="package-scope-title">Plan the whole setup.</h2></div><p>Your quotation brings the drone and supporting equipment into one clear build scope. Confirm the exact inclusions for your selected package with AVX.</p></div>
                <ul class="packages-build-scope">
                    @foreach ($buildScope as $item)
                        <li data-package-reveal><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $item['path'] }}" /></svg><h3>{{ $item['title'] }}</h3><p>{{ $item['detail'] }}</p></li>
                    @endforeach
                </ul>
                <p class="packages-scope-note">Equipment brands, battery specifications, FPV setup, and other package inclusions are itemized in your final quotation.</p>
            </div>
        </section>

        <section id="package-comparison" class="packages-comparison package-shell" aria-labelledby="package-comparison-title">
            <div class="packages-group-heading" data-package-reveal><div><p class="package-eyebrow">Side by side</p><h2 id="package-comparison-title">Find your starting point.</h2></div><p>Compare the four options, then tell us which one you’d like to discuss. You don’t need a complete technical brief to start a conversation.</p></div>
            <div class="packages-table-wrap" tabindex="0" role="region" aria-label="Drone package comparison; scroll horizontally on small screens">
                <table class="packages-table"><caption class="sr-only">Drone package names, indicative prices, and intended application</caption><thead><tr><th scope="col">Package</th><th scope="col">Indicative price</th><th scope="col">Application</th><th scope="col"><span class="sr-only">Inquire</span></th></tr></thead><tbody>
                    @foreach ($packages as $package)
                        <tr><th scope="row">{{ $package['title'] }}</th><td>{{ $package['starts_at'] ? 'From ' : '' }}₱{{ number_format($package['price']) }}</td><td>{{ $package['focus'] }}</td><td><a href="{{ route('drone-systems', ['package' => $package['id']]) }}#contact" data-package-inquire data-package-title="{{ $package['title'] }}" aria-label="Inquire about {{ $package['title'] }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg></a></td></tr>
                    @endforeach
                </tbody></table>
            </div>
            <p class="packages-price-note">All amounts are indicative, not a final order total. Request a quotation to confirm the build, equipment, applicable taxes, delivery, and availability.</p>
        </section>

    </main>
    <x-site-contact :subject="$selectedPackage ? 'Drone package inquiry: '.$selectedPackage['title'] : 'Drone package inquiry'" brief-title="Your package inquiry" />
</x-site-layout>
