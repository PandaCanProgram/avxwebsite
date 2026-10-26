@php
    $interestAreas = ['Exploring opportunities', 'Drone & UAV systems', 'Software & AI development', 'Engineering & product design'];
    $inputClasses = 'investment-input aria-[invalid=true]:border-red-400 aria-[invalid=true]:bg-red-950/20';
@endphp

<form id="investor-inquiry" action="mailto:avxengineeringsolutions@gmail.com?subject=Investment%20Inquiry" method="POST" enctype="text/plain" class="investment-form" aria-labelledby="investor-form-title" aria-describedby="investor-form-help" data-investment-form data-validate>
    <div class="investment-form-header">
        <p class="investment-eyebrow">Let's connect</p>
        <span class="investment-form-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 19 19 5M5 5h14v14" /></svg>
        </span>
    </div>
    <h2 id="investor-form-title" class="investment-form-title">Start a conversation</h2>
    <p id="investor-form-help" class="investment-form-description">Tell us a little about yourself and your interest in AVX.</p>

    <div class="investment-fields">
        <div>
            <label for="investor-name">Full name <span aria-hidden="true">*</span></label>
            <input id="investor-name" name="name" type="text" autocomplete="name" required maxlength="100" placeholder="Your full name" data-required-message="Please enter your full name." class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-email">Email address <span aria-hidden="true">*</span></label>
            <input id="investor-email" name="email" type="email" autocomplete="email" required maxlength="254" placeholder="you@example.com" data-required-message="Please enter your email address." class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-organization">Organization <span>(optional)</span></label>
            <input id="investor-organization" name="organization" type="text" autocomplete="organization" maxlength="150" placeholder="Company or organization" class="{{ $inputClasses }}">
        </div>
        <div>
            <label for="investor-phone">Phone number <span>(optional)</span></label>
            <input id="investor-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" placeholder="Include country code" class="{{ $inputClasses }}">
        </div>
        <div class="investment-field-wide">
            <label for="investor-interest">Area of interest</label>
            <select id="investor-interest" name="interest" class="{{ $inputClasses }}">
                @foreach ($interestAreas as $interestArea)
                    <option value="{{ $interestArea }}">{{ $interestArea }}</option>
                @endforeach
            </select>
        </div>
        <div class="investment-field-wide">
            <label for="investor-message">Your message <span aria-hidden="true">*</span></label>
            <textarea id="investor-message" name="message" rows="4" required maxlength="3000" placeholder="Share your interest in AVX and what you would like to discuss." data-required-message="Please tell us about your interest in AVX." class="{{ $inputClasses }}"></textarea>
        </div>
    </div>

    <button type="submit" class="investment-submit"><span>Continue with inquiry</span><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6" /></svg></button>
    <p class="investment-form-note">Next, choose Gmail, Viber, or Messenger to review and send your inquiry. Fields marked * are required.</p>
    <noscript><p class="investment-form-note">This form uses JavaScript to prepare your inquiry. You can also email <a href="mailto:avxengineeringsolutions@gmail.com?subject=Investment%20Inquiry">avxengineeringsolutions@gmail.com</a> directly.</p></noscript>
</form>

<x-site-send-options id-prefix="investor-send" />
