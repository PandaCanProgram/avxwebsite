@php
    $selected = old('recipient_id', '');
    $selected = is_scalar($selected) ? (string) $selected : '';
@endphp

<x-admin-shell title="New Message">
    <main id="main-content" class="admin-dashboard">
        <a class="admin-text-link admin-users-back" href="{{ route('admin.messages.index') }}">&larr; Back to Messages</a>
        <p class="admin-eyebrow">Private Conversation</p>
        <h1>New Message</h1>
        <p class="admin-dashboard-intro">Choose a team member to start a private conversation.</p>
        @if ($users->isEmpty())
            <p class="admin-notice">Add a team member in User Roles first. <a class="admin-text-link" href="{{ route('admin.users.create') }}">Add User &rarr;</a></p>
        @endif
        <form class="admin-setting-card admin-user-form" method="POST" action="{{ route('admin.messages.direct.store') }}" novalidate>
            @csrf
            <div class="admin-field">
                <label for="message-recipient">Message To</label>
                <div class="admin-select-wrap">
                    <select id="message-recipient" name="recipient_id" required aria-describedby="message-recipient-help{{ $errors->directMessage->has('recipient_id') ? ' message-recipient-error' : '' }}" @if ($errors->directMessage->has('recipient_id')) aria-invalid="true" @endif @disabled($users->isEmpty())>
                        <option value="">Choose a user</option>
                        @foreach ($users as $member)
                            <option value="{{ $member->id }}" @selected($selected === (string) $member->id)>{{ $member->name }} &mdash; {{ $member->position ?? 'Team Member' }} ({{ $member->email }})</option>
                        @endforeach
                    </select>
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
                </div>
                <p class="admin-field-help" id="message-recipient-help">This conversation includes only you and the selected user. If you have already messaged them, your existing conversation will open.</p>
                @error('recipient_id', 'directMessage')<p class="admin-field-error" id="message-recipient-error" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="admin-setting-actions admin-user-form-actions"><a class="admin-user-edit" href="{{ route('admin.messages.index') }}">Cancel</a><button class="admin-button" type="submit" @disabled($users->isEmpty())>Open Conversation &rarr;</button></div>
        </form>
    </main>
</x-admin-shell>
