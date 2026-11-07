@php
    $interestAreas = config('inquiries.interest_areas');
    $inputClasses = 'investment-input aria-[invalid=true]:border-red-400 aria-[invalid=true]:bg-red-950/20';
@endphp

<form id="investor-inquiry" action="{{ route('inquiries.investment') }}" method="POST" class="investment-form" aria-labelledby="investor-form-title" aria-describedby="investor-form-help investor-delivery-note" data-investment-form data-inquiry-form data-validate @if ($errors->inquiry->any() || session('inquirySubmitted')) data-inquiry-restore @endif>
    @csrf
    <input type="hidden" name="submission_id" value="{{ old('submission_id', (string) Illuminate\Support\Str::uuid()) }}">
    <input type="hidden" name="source" value="investment">
    <div class="inquiry-honeypot" aria-hidden="true"><label for="investor-extra">Leave this empty</label><input id="investor-extra" name="inquiry_extra" type="text" value="" readonly tabindex="-1" autocomplete="off" data-lpignore="true" data-1p-ignore></div>
    <div class="investment-form-header">
        <p class="investment-eyebrow">Let's Connect</p>
        <span class="investment-form-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 19 19 5M5 5h14v14" /></svg>
        </span>
    </div>
    <h2 id="investor-form-title" class="investment-form-title">Start a Conversation</h2>
    <p id="investor-form-help" class="investment-form-description">Connect with our team about your interest in AVX.</p>

    <fieldset class="investment-fields">
        <legend class="sr-only">Investor details</legend>
        <div>
            <label for="investor-name">Full name <span aria-hidden="true">*</span></label>
            <input id="investor-name" name="name" type="text" autocomplete="name" required maxlength="100" value="{{ old('name') }}" placeholder="Your full name" data-required-message="Please enter your full name." class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-email">Email address <span aria-hidden="true">*</span></label>
            <input id="investor-email" name="email" type="email" autocomplete="email" required maxlength="254" value="{{ old('email') }}" placeholder="you@example.com" data-email-check-url="{{ route('inquiries.check-email') }}" data-required-message="Please enter your email address." class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-organization">Organization <span>(optional)</span></label>
            <input id="investor-organization" name="organization" type="text" autocomplete="organization" maxlength="150" value="{{ old('organization') }}" placeholder="Company or organization" class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-phone">Phone number <span>(optional)</span></label>
            <input id="investor-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" minlength="7" maxlength="40" value="{{ old('phone') }}" placeholder="Include country code" class="{{ $inputClasses }}">
        </div>
        <div class="investment-field-wide">
            <label for="investor-interest">Area of interest</label>
            <select id="investor-interest" name="interest" class="{{ $inputClasses }}">
                @foreach ($interestAreas as $interestArea)
                    <option value="{{ $interestArea }}" @selected(old('interest') === $interestArea)>{{ $interestArea }}</option>
                @endforeach
            </select>
        </div>
        <div class="investment-field-wide">
            <label for="investor-message">Your message <span aria-hidden="true">*</span></label>
            <textarea id="investor-message" name="message" rows="4" required minlength="10" maxlength="5000" placeholder="Share your interest in AVX and what you would like to discuss." data-required-message="Please tell us about your interest in AVX." class="{{ $inputClasses }}">{{ old('message') }}</textarea>
        </div>
    </fieldset>

    <button type="submit" class="investment-submit"><span data-submit-label>Send Inquiry</span><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6" /></svg></button>
    <p id="investor-delivery-note" class="investment-form-note">Your inquiry goes directly to our team. We'll email you an acknowledgment after submission.</p>
    <div class="inquiry-status" data-inquiry-status role="status" aria-live="polite" @if (! session('inquirySubmitted') && ! $errors->inquiry->any()) hidden @endif>
        @if (session('inquirySubmitted'))
            {{ session('inquirySubmitted') }}
        @elseif ($errors->inquiry->any())
            {{ $errors->inquiry->first() }} Please review your details and try again.
        @endif
    </div>
</form>
