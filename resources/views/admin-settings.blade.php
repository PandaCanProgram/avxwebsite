<x-admin-shell title="Settings">
    <main id="main-content" class="admin-dashboard admin-settings">
        <p class="admin-eyebrow">Your Workspace</p>
        <h1>Settings</h1>
        <p class="admin-dashboard-intro">Manage your account and keep the website running your way.</p>

        <section id="appearance" class="admin-setting-card" aria-labelledby="appearance-title">
            <div class="admin-setting-heading"><span class="admin-setting-number" aria-hidden="true">01</span><div><h2 id="appearance-title">Appearance</h2><p>Choose how your admin workspace looks. Your preference is saved to your account.</p></div></div>
            @if (session('appearanceUpdated'))
                <p class="admin-notice" role="status">{{ session('appearanceUpdated') }}</p>
            @endif
            <form method="POST" action="{{ route('admin.settings.appearance') }}" data-admin-theme-form>
                @csrf
                @method('PATCH')
                <fieldset class="admin-theme-options" @error('theme', 'appearanceUpdate') aria-describedby="theme-error" @enderror>
                    <legend class="admin-sr-only">Color theme</legend>
                    <label class="admin-theme-choice">
                        <input type="radio" name="theme" value="light" @checked(auth()->user()->admin_theme !== 'dark')>
                        <span class="admin-theme-preview admin-theme-preview-light" aria-hidden="true"><span></span><span></span></span>
                        <span><strong>Light</strong><span>Bright and clear</span></span>
                    </label>
                    <label class="admin-theme-choice">
                        <input type="radio" name="theme" value="dark" @checked(auth()->user()->admin_theme === 'dark')>
                        <span class="admin-theme-preview admin-theme-preview-dark" aria-hidden="true"><span></span><span></span></span>
                        <span><strong>Dark</strong><span>A softer view after hours</span></span>
                    </label>
                </fieldset>
                @error('theme', 'appearanceUpdate')
                    <p class="admin-field-error" id="theme-error" role="alert">{{ $message }}</p>
                @enderror
                <div class="admin-setting-actions"><button class="admin-button" type="submit">Save Appearance</button></div>
            </form>
        </section>

        <section id="password" class="admin-setting-card" aria-labelledby="password-title">
            <div class="admin-setting-heading"><span class="admin-setting-number" aria-hidden="true">02</span><div><h2 id="password-title">Change Password</h2><p>Use at least 12 characters. Enter your current password to save a new one.</p></div></div>
            @if (session('passwordUpdated'))
                <p class="admin-notice" role="status">{{ session('passwordUpdated') }}</p>
            @endif
            <form method="POST" action="{{ route('admin.settings.password') }}" class="admin-password-form">
                @csrf
                @method('PATCH')
                <div class="admin-field">
                    <label for="current-password">Current Password</label>
                    <input id="current-password" name="current_password" type="password" autocomplete="current-password" required @error('current_password', 'passwordUpdate') aria-invalid="true" aria-describedby="current-password-error" @enderror>
                    @error('current_password', 'passwordUpdate')
                        <p class="admin-field-error" id="current-password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="admin-password-grid">
                    <div class="admin-field">
                        <label for="new-password">New Password</label>
                        <input id="new-password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required @error('password', 'passwordUpdate') aria-invalid="true" aria-describedby="new-password-error" @enderror>
                        @error('password', 'passwordUpdate')
                            <p class="admin-field-error" id="new-password-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="admin-field">
                        <label for="confirm-password">Confirm New Password</label>
                        <input id="confirm-password" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                    </div>
                </div>
                <div class="admin-setting-actions"><button class="admin-button" type="submit">Update Password</button></div>
            </form>
        </section>

        <section id="maintenance" class="admin-setting-card" aria-labelledby="maintenance-title">
            <div class="admin-setting-heading"><span class="admin-setting-number" aria-hidden="true">03</span><div><h2 id="maintenance-title">Website Maintenance</h2><p>Control whether visitors can browse the public website.</p></div></div>
            @if (session('maintenanceUpdated'))
                <p class="admin-notice" role="status">{{ session('maintenanceUpdated') }}</p>
            @endif
            <div class="admin-website-status" data-status="{{ $maintenanceEnabled ? 'maintenance' : 'live' }}"><span aria-hidden="true"></span><strong>{{ $maintenanceEnabled ? 'Maintenance Mode Is On' : 'Website Is Live' }}</strong></div>
            <form method="POST" action="{{ route('admin.settings.maintenance') }}">
                @csrf
                @method('PATCH')
                <label class="admin-maintenance-option" for="maintenance-enabled">
                    <span><strong>Enable Maintenance Mode</strong><span id="maintenance-help">Visitors will see a temporary maintenance page. Admin sign-in and Settings will stay available.</span></span>
                    <input name="maintenance_enabled" type="hidden" value="0">
                    <input id="maintenance-enabled" name="maintenance_enabled" type="checkbox" role="switch" value="1" @checked($maintenanceEnabled) aria-describedby="maintenance-help">
                </label>
                @error('maintenance_enabled', 'maintenanceUpdate')
                    <p class="admin-field-error" role="alert">{{ $message }}</p>
                @enderror
                <div class="admin-setting-actions"><p>The change takes effect when you save.</p><button class="admin-button" type="submit">Save Website Status</button></div>
            </form>
        </section>
    </main>
</x-admin-shell>
