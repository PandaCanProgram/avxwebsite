<section id="investment" class="investment-scene" data-scene data-scene-label="Investment" aria-labelledby="investment-callout-title">
    <div class="investment-atmosphere" aria-hidden="true">
        <div class="investment-grid" data-parallax="0.05"></div>
        <div class="investment-callout-orbit" data-parallax="-0.1"><span></span><span></span></div>
    </div>

    <div class="investment-callout mx-auto max-w-7xl px-4 sm:px-8">
        <div class="investment-callout-copy">
            <p class="investment-eyebrow" data-reveal>Invest in AVX</p>
            <h2 id="investment-callout-title" class="investment-callout-title" data-reveal>Interested in investing?<br><span>Let's build what's next.</span></h2>
            <p class="investment-description" data-reveal>Be part of AVX's next chapter in UAV systems, software, and engineering design. Share your interest and connect with our team to explore the possibilities.</p>
        </div>

        <div class="investment-callout-action" data-reveal>
            <a href="{{ route('investment') }}" class="investment-inquire" data-investment-open aria-haspopup="dialog" aria-controls="investor-inquiry-dialog">
                <span>Inquire</span>
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" /></svg>
            </a>
            <p>Start a conversation with our team.</p>
        </div>
    </div>

    <dialog id="investor-inquiry-dialog" class="investment-dialog" data-investment-dialog aria-labelledby="investor-form-title" aria-describedby="investor-form-help">
        <div class="investment-dialog-panel">
            <button type="button" class="investment-dialog-close" data-investment-close aria-label="Close investor inquiry" autofocus>
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6" /></svg>
            </button>
            <div class="investment-inquiry">
                <x-site-investor-form />
            </div>
        </div>
    </dialog>
</section>
