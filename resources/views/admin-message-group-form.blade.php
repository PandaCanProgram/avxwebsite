@php
    $editing = $group !== null;
    $groupName = old('name', $group?->name ?? '');
    $groupName = is_string($groupName) ? $groupName : '';
    $selected = old('members', $group?->members->where('is_admin', false)->modelKeys() ?? []);
    $selected = is_array($selected) ? array_map('strval', array_filter($selected, 'is_scalar')) : [];
    $memberError = $errors->messageGroup->first('members') ?: $errors->messageGroup->first('members.*');
@endphp

<x-admin-shell :title="$editing ? 'Manage Group' : 'New Group'">
    <main id="main-content" class="admin-dashboard">
        <a class="admin-text-link admin-users-back" href="{{ $editing ? route('admin.messages.show', $group) : route('admin.messages.index') }}">&larr; Back to Messages</a>
        <p class="admin-eyebrow">Team Conversations</p>
        <h1>{{ $editing ? 'Manage Group' : 'Create a Group' }}</h1>
        <p class="admin-dashboard-intro">{{ $editing ? 'Rename your group or change who is part of the conversation.' : 'Bring the right people together in one conversation.' }}</p>
        @if ($users->isEmpty() && ! $editing)
            <p class="admin-notice">Add team members in User Roles first. <a class="admin-text-link" href="{{ route('admin.users.create') }}">Add User &rarr;</a></p>
        @endif
        <form class="admin-setting-card admin-user-form" method="POST" action="{{ $editing ? route('admin.messages.update', $group) : route('admin.messages.store') }}" data-chat-group-form novalidate>
            @csrf
            @if ($editing) @method('PATCH') @endif
            <div class="admin-field">
                <label for="group-name">Group Name</label>
                <input id="group-name" name="name" value="{{ $groupName }}" required maxlength="120" placeholder="e.g. AVX Software Team" @error('name', 'messageGroup') aria-invalid="true" aria-describedby="group-name-error" @enderror>
                @error('name', 'messageGroup')<p id="group-name-error" class="admin-field-error" role="alert">{{ $message }}</p>@enderror
            </div>
            <fieldset class="admin-user-fieldset admin-user-password-section">
                <legend>Group Members <span class="admin-participant-count" data-chat-member-count>{{ $users->filter(fn ($member) => in_array((string) $member->id, $selected, true))->count() }} selected</span></legend>
                <p class="admin-field-help" id="group-members-help">Toggle users on to add them, or off to remove them. {{ $editing ? 'You can remove all team members and keep the group history.' : 'Choose at least one team member. There is no two-person limit.' }}</p>
                <input type="hidden" name="members[]" value="">
                @if ($memberError)<p class="admin-field-error" role="alert">{{ $memberError }}</p>@endif
                <div class="admin-chat-owner"><span class="admin-avatar" aria-hidden="true">{{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }} (You)</strong><span>Group manager &middot; always included</span></div><span class="admin-access-badge">Admin</span></div>
                <div class="admin-participant-panel">
                    <div class="admin-participant-list" aria-describedby="group-members-help">
                        @forelse ($users as $member)
                            <label class="admin-participant-choice" for="chat-member-{{ $member->id }}">
                                <span class="admin-avatar" aria-hidden="true">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</span>
                                <span class="admin-participant-identity"><strong>{{ $member->name }}</strong><span>{{ $member->position ?? 'Team Member' }}</span><small>{{ $member->email }}</small></span>
                                <input id="chat-member-{{ $member->id }}" type="checkbox" role="switch" name="members[]" value="{{ $member->id }}" data-chat-member @checked(in_array((string) $member->id, $selected, true))>
                            </label>
                        @empty
                            <p class="admin-field-help">No team members available yet.</p>
                        @endforelse
                    </div>
                </div>
                @if ($editing)<p class="admin-field-help">Changing members keeps all previous messages. Only you can manage this group.</p>@endif
            </fieldset>
            <div class="admin-setting-actions admin-user-form-actions"><a class="admin-user-edit" href="{{ $editing ? route('admin.messages.show', $group) : route('admin.messages.index') }}">Cancel</a><button class="admin-button" type="submit" @disabled(! $editing && $users->isEmpty())>{{ $editing ? 'Save Changes' : 'Create Group' }}</button></div>
        </form>
    </main>
</x-admin-shell>
