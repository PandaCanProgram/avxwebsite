@php
    $chapters = [
        'intro' => 'Introduction',
        'strengths' => 'Our Strengths',
        'about' => 'About Us',
        'services' => 'Drone & UAV',
        'programming' => 'Programming',
        'projects' => 'Featured Projects',
        'testimonials' => 'Client Feedback',
        'faq' => 'FAQ',
        'contact' => 'Get in Touch',
    ];
@endphp

<div class="story-controls" data-story-controls hidden>
    <div class="story-progress" aria-hidden="true"><span data-story-progress></span></div>

    <nav class="story-chapters" aria-label="Page chapters">
        @foreach ($chapters as $id => $label)
            <a href="#{{ $id }}" class="story-chapter" data-chapter-link aria-label="{{ $loop->iteration }}. {{ $label }}" @if ($loop->first) aria-current="step" @endif>
                <span class="story-chapter-label">{{ $label }}</span>
                <span class="story-chapter-dot" aria-hidden="true"></span>
            </a>
        @endforeach
    </nav>

    <div class="story-console">
        <span class="story-position" aria-hidden="true"><span data-story-position>01</span><span class="story-position-total"> / {{ str_pad(count($chapters), 2, '0', STR_PAD_LEFT) }}</span></span>
        <span class="story-current" data-story-current aria-hidden="true">Introduction</span>
        <div class="story-console-actions">
            <button type="button" data-chapter-prev aria-label="Previous chapter" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m6 14 6-6 6 6" /></svg>
            </button>
            <button type="button" data-chapter-next aria-label="Next chapter">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m6 10 6 6 6-6" /></svg>
            </button>
            <button type="button" class="story-motion" data-motion-toggle aria-pressed="true" aria-label="Scroll animations">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /><circle cx="9" cy="7" r="2" /><circle cx="15" cy="12" r="2" /><circle cx="9" cy="17" r="2" /></svg>
                <span data-motion-label>Motion on</span>
            </button>
        </div>
    </div>
</div>
