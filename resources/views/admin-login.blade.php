<x-admin-layout title="Sign In">
    <main id="main-content" class="admin-login-shell">
        <div class="admin-login-card">
            <section class="admin-login-brand" aria-labelledby="admin-brand-title">
                <a class="admin-brand" href="{{ route('home') }}" aria-label="AVX Engineering Solutions homepage">
                    <img src="{{ asset('avx-logo.webp') }}" width="52" height="52" alt="">
                    <span><strong>AVX</strong><span>Engineering Solutions</span></span>
                </a>
                <div class="admin-brand-copy">
                    <p class="admin-eyebrow">Administration</p>
                    <h1 id="admin-brand-title">Your workspace.<br><span>Ready for what's next.</span></h1>
                    <p>Sign in to access the AVX admin workspace.</p>
                </div>
                <a class="admin-back-link" href="{{ route('home') }}"><span aria-hidden="true">&larr;</span> Back to Website</a>
            </section>

            <section class="admin-login-form-panel" aria-labelledby="admin-login-title">
                <div class="admin-login-heading">
                    <span class="admin-lock-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/></svg></span>
                    <h2 id="admin-login-title">Welcome Back</h2>
                    <p>Enter your admin email and password.</p>
                </div>

                @if (session('status'))
                    <p class="admin-notice" role="status">{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="admin-login-form">
                    @csrf
                    <div class="admin-field">
                        <label for="admin-email">Email Address</label>
                        <input id="admin-email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@avxengineering.com" autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="254" required autofocus @error('email') aria-invalid="true" aria-describedby="admin-email-error" @enderror>
                        @error('email')
                            <p id="admin-email-error" class="admin-field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="admin-field">
                        <label for="admin-password">Password</label>
                        <input id="admin-password" name="password" type="password" autocomplete="current-password" maxlength="1024" required @error('password') aria-invalid="true" aria-describedby="admin-password-error" @enderror>
                        @error('password')
                            <p id="admin-password-error" class="admin-field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="admin-button">Sign In <span aria-hidden="true">&rarr;</span></button>
                </form>
                <p class="admin-login-footnote">For authorized AVX administrators.</p>
            </section>
        </div>
    </main>
</x-admin-layout>
