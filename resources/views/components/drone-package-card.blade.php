@props(['package', 'number' => 1])

<article id="{{ $package['id'] }}" class="drone-package-card" data-package-card data-package-reveal>
    <div class="package-card-topline"><span>AVX / {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $package['label'] }}</span></div>
    <div class="package-card-visual">
        <x-drone-package-diagram :kind="$package['group']" />
        <div class="package-card-capacity"><strong>{{ $package['figure'] }}</strong><span>{{ $package['unit'] }}</span></div>
        <span class="package-illustration-label">Platform illustration</span>
    </div>
    <div class="package-card-body">
        <p class="package-kicker">{{ $package['group'] === 'field' ? 'Lifter / Sprayer' : 'Development platform' }}</p>
        <h3>{{ $package['title'] }}</h3>
        <p class="package-card-description">{{ $package['description'] }}</p>
        <div class="package-price"><span>{{ $package['starts_at'] ? 'Starts at · indicative' : 'Indicative package price' }}</span><strong><span>₱</span>{{ number_format($package['price']) }}</strong></div>
        <ul class="package-card-details">
            @foreach ($package['details'] as $detail)
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>{{ $detail }}</li>
            @endforeach
        </ul>
        <a class="package-button" href="{{ route('drone-systems', ['package' => $package['id']]) }}#contact" data-package-inquire data-package-title="{{ $package['title'] }}">Inquire about this package <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg></a>
    </div>
</article>
