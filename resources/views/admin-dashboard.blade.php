<x-admin-shell title="Dashboard">
    <main id="main-content" class="admin-dashboard">
        <p class="admin-eyebrow">AVX Workspace</p>
        <h1>Dashboard</h1>
        <p class="admin-dashboard-intro">Welcome back, {{ auth()->user()->name }}.</p>
        @include('partials.admin-job-calendar')
        <section class="admin-welcome-panel" aria-labelledby="admin-workspace-title">
            <div class="admin-welcome-mark" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m5 12 4 4L19 6"/></svg></div>
            <div>
                <h2 id="admin-workspace-title">You're Signed In</h2>
                <p>Manage your password, appearance, and website availability in <a href="{{ route('admin.settings') }}">Settings</a>.</p>
            </div>
        </section>
        <section class="admin-account-panel" aria-labelledby="admin-account-title">
            <h2 id="admin-account-title">Your Account</h2>
            <dl class="admin-account-details">
                <div><dt>Name</dt><dd>{{ auth()->user()->name }}</dd></div>
                <div><dt>Email</dt><dd>{{ auth()->user()->email }}</dd></div>
                <div><dt>Access</dt><dd><span class="admin-access-badge">Administrator</span></dd></div>
            </dl>
        </section>
    </main>
</x-admin-shell>
