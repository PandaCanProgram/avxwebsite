@php
    $chapters = [
        'intro' => 'Introduction',
        'about' => 'About Us',
        'services' => 'Drone & UAV',
        'programming' => 'Programming',
        'projects' => 'Featured Projects',
        'testimonials' => 'Client Feedback',
        'faq' => 'FAQ',
        'contact' => 'Get in Touch',
        'investment' => 'For Investors',
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

</div>
