@props(['title'])

<x-admin-layout :title="$title">
    <header class="admin-header">
        <div class="admin-header-inner">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="AVX admin dashboard">
                <img src="{{ asset('avx-logo.webp') }}" width="44" height="44" alt="">
                <span><strong>AVX</strong><span>Administration</span></span>
            </a>
            <div class="admin-header-actions">
                <a href="{{ route('home') }}">View Website <span aria-hidden="true">&nearr;</span></a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-signout">Sign Out</button>
                </form>
            </div>
        </div>
    </header>
    <div class="admin-workspace">
        <aside class="admin-sidebar" aria-label="Admin navigation">
            <p class="admin-sidebar-label">Workspace</p>
            <nav aria-label="Main">
                <a href="{{ route('admin.messages.index') }}" class="admin-nav-link" @if (request()->routeIs('admin.messages.*')) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-2 2v-8.5A8.5 8.5 0 0 1 10.5 5H13a8 8 0 0 1 8 6.5Z"/><path d="M7 11h9M7 15h6"/></svg>
                    <span>Messages</span><span class="admin-nav-arrow" aria-hidden="true">&rsaquo;</span>
                </a>
                <a href="{{ route('admin.job-orders.index') }}" class="admin-nav-link" @if (request()->routeIs('admin.job-orders.*')) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2M9 9h6m-6 4h6m-6 4h4"/></svg>
                    <span>Job Orders</span><span class="admin-nav-arrow" aria-hidden="true">&rsaquo;</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="admin-nav-link" @if (request()->routeIs('admin.users.*')) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="9" cy="7" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3m1-17a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 4v3"/></svg>
                    <span>User Roles</span><span class="admin-nav-arrow" aria-hidden="true">&rsaquo;</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="admin-nav-link" @if (request()->routeIs('admin.settings*')) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m9 3-.5 2-2 .9-1.9-.6-2 3.4L4 10v2l-1.4 1.3 2 3.4 1.9-.6 2 .9.5 2h4l.5-2 2-.9 1.9.6 2-3.4L20 12v-2l1.4-1.3-2-3.4-1.9.6-2-.9L15 3z"/><circle cx="12" cy="11" r="3"/></svg>
                    <span>Settings</span><span class="admin-nav-arrow" aria-hidden="true">&rsaquo;</span>
                </a>
            </nav>
            <div class="admin-sidebar-account"><span class="admin-avatar" aria-hidden="true">A</span><div><strong>Administrator</strong><span>{{ auth()->user()->email }}</span></div></div>
        </aside>
        <div class="admin-workspace-content">{{ $slot }}</div>
    </div>
</x-admin-layout>
