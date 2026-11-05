<section id="alpha-registration" class="alpha-registration" aria-label="Alpha-test pre-registration">
    <div class="alpha-registration-inner">
        <div class="alpha-registration-invite">
            <span class="alpha-registration-badge"><span aria-hidden="true"></span> Alpha access</span>
            <p class="alpha-registration-pitch"><strong>Be first to test what's next.</strong><span>Your feedback. AVX's next breakthrough.</span></p>
        </div>
        <button type="button" class="alpha-registration-trigger" data-alpha-registration-open aria-haspopup="dialog" aria-controls="alpha-registration-dialog">Pre-register <span class="alpha-registration-arrow" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M5 12h14m-6-6 6 6-6 6" /></svg></span></button>
    </div>
</section>

<dialog id="alpha-registration-dialog" class="alpha-registration-dialog" data-alpha-registration-dialog aria-labelledby="alpha-registration-title" aria-describedby="alpha-registration-description" @if ($errors->alphaRegistration->any() || session('alphaRegistrationSubmitted')) data-alpha-registration-restore open @endif>
    <div class="alpha-registration-dialog-heading">
        <p class="alpha-registration-eyebrow">DICT week / Early access</p>
        <button type="button" class="alpha-registration-close" data-alpha-registration-close aria-label="Close pre-registration">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
    </div>
    <h2 id="alpha-registration-title">Be first to test what's next.</h2>
    <p id="alpha-registration-description">Join the list for upcoming alpha tests of AVX's internal projects. We'll invite you when testing opens.</p>

    <form action="{{ route('alpha-registration.store') }}" method="POST" class="alpha-registration-form" data-alpha-registration-form>
        @csrf
        <input type="hidden" name="source" value="{{ request()->route()?->getName() ?? 'home' }}">
        <div class="alpha-registration-trap" aria-hidden="true">
            <label for="alpha-website">Leave this field empty</label>
            <input id="alpha-website" type="text" name="alpha_website" tabindex="-1" autocomplete="off">
        </div>
        <label for="alpha-name">Your name</label>
        <input id="alpha-name" type="text" name="alpha_name" value="{{ old('alpha_name') }}" placeholder="Your full name" autocomplete="name" maxlength="100" required autofocus aria-describedby="alpha-registration-status" @if ($errors->alphaRegistration->has('alpha_name')) aria-invalid="true" @endif>

        <label for="alpha-email">Email address</label>
        <input id="alpha-email" type="email" name="alpha_email" value="{{ old('alpha_email') }}" placeholder="you@example.com" autocomplete="email" inputmode="email" maxlength="254" required aria-describedby="alpha-registration-note alpha-registration-status" @if ($errors->alphaRegistration->has('alpha_email')) aria-invalid="true" @endif>

        <p id="alpha-registration-note" class="alpha-registration-note">By pre-registering, you agree to receive an email invitation from AVX when alpha testing opens.</p>
        <button type="submit" class="alpha-registration-submit">Pre-register <span aria-hidden="true">&rarr;</span></button>
        <p id="alpha-registration-status" class="alpha-registration-status" role="status" aria-live="polite" aria-atomic="true" tabindex="-1" data-alpha-registration-status @if ($errors->alphaRegistration->any()) data-state="error" @elseif (session('alphaRegistrationSubmitted')) data-state="success" @else hidden @endif>{{ $errors->alphaRegistration->first() ?: session('alphaRegistrationSubmitted') }}</p>
    </form>
</dialog>
<noscript>
    <style>
        .alpha-registration-trigger, .alpha-registration-close { display: none; }
        .alpha-registration-dialog:not([open]) { display: block; position: static; margin: 24px auto; }
    </style>
</noscript>
