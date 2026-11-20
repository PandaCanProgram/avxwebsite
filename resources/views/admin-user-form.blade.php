@php
    $editing = $user !== null;
    $fieldValue = function (string $field) use ($user): string {
        $value = old($field, $user?->{$field} ?? '');

        return is_string($value) ? $value : '';
    };
@endphp

<x-admin-shell :title="$editing ? 'Edit User' : 'Add User'">
    <main id="main-content" class="admin-dashboard">
        <a class="admin-text-link admin-users-back" href="{{ route('admin.users.index') }}"><span aria-hidden="true">&larr;</span> Back to User Roles</a>
        <p class="admin-eyebrow">Your Team</p>
        <h1>{{ $editing ? 'Edit User' : 'Add User' }}</h1>
        <p class="admin-dashboard-intro">{{ $editing ? 'Update this team member\'s account details and position.' : 'Set up an account and assign a position for your team member.' }}</p>

        <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-user-form admin-setting-card" data-admin-user-form novalidate>
            @csrf
            @if ($editing)
                @method('PATCH')
            @endif
            <fieldset class="admin-user-fieldset">
                <legend>Account Details</legend>
                <div class="admin-user-form-grid">
                    <div class="admin-field">
                        <label for="user-name">Full Name</label>
                        <input id="user-name" name="name" type="text" value="{{ $fieldValue('name') }}" autocomplete="section-team name" maxlength="100" required @error('name', 'userDetails') aria-invalid="true" aria-describedby="user-name-error" autofocus @enderror>
                        <p id="user-name-error" class="admin-field-error" data-admin-field-error="name" role="alert" @if (! $errors->userDetails->has('name')) hidden @endif>{{ $errors->userDetails->first('name') }}</p>
                    </div>
                    <div class="admin-field">
                        <label for="user-email">Email Address</label>
                        <input id="user-email" name="email" type="email" value="{{ $fieldValue('email') }}" autocomplete="section-team email" autocapitalize="none" spellcheck="false" maxlength="254" required @error('email', 'userDetails') aria-invalid="true" aria-describedby="user-email-error" @enderror>
                        <p id="user-email-error" class="admin-field-error" data-admin-field-error="email" role="alert" @if (! $errors->userDetails->has('email')) hidden @endif>{{ $errors->userDetails->first('email') }}</p>
                    </div>
                    <div class="admin-field admin-user-position-field">
                        <label for="user-position">Position / Role</label>
                        <div class="admin-select-wrap">
                            <select id="user-position" name="position" required aria-describedby="user-position-help{{ $errors->userDetails->has('position') ? ' user-position-error' : '' }}" @error('position', 'userDetails') aria-invalid="true" @enderror>
                                <option value="" disabled @selected(! in_array($fieldValue('position'), config('admin.positions'), true))>Select a position</option>
                                @foreach (config('admin.positions') as $position)
                                    <option value="{{ $position }}" @selected($fieldValue('position') === $position)>{{ $position }}</option>
                                @endforeach
                            </select>
                            <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p id="user-position-help" class="admin-field-help">Select Software or Hardware. Team accounts do not have admin access.
                            @if ($editing && filled($user->position) && ! in_array($user->position, config('admin.positions'), true))
                                <span>Current saved position: {{ $user->position }}.</span>
                            @endif
                        </p>
                        <p id="user-position-error" class="admin-field-error" data-admin-field-error="position" role="alert" @if (! $errors->userDetails->has('position')) hidden @endif>{{ $errors->userDetails->first('position') }}</p>
                    </div>
                </div>
            </fieldset>

            <fieldset class="admin-user-fieldset admin-user-password-section">
                <legend>{{ $editing ? 'Reset Password' : 'Set Password' }}</legend>
                <p class="admin-field-help">{{ $editing ? 'Leave both password fields blank to keep the current password. To reset it, enter and confirm a new password with at least 8 characters.' : 'Create a password with at least 8 characters. Passwords are stored securely and cannot be viewed later.' }}</p>
                <div class="admin-user-form-grid">
                    <div class="admin-field">
                        <label for="user-password">{{ $editing ? 'New Password' : 'Password' }}</label>
                        <input id="user-password" name="password" type="password" autocomplete="new-password" minlength="8" maxlength="72" @required(! $editing) @error('password', 'userDetails') aria-invalid="true" aria-describedby="user-password-error" @enderror>
                        <p id="user-password-error" class="admin-field-error" data-admin-field-error="password" role="alert" @if (! $errors->userDetails->has('password')) hidden @endif>{{ $errors->userDetails->first('password') }}</p>
                    </div>
                    <div class="admin-field">
                        <label for="user-password-confirmation">Confirm Password</label>
                        <input id="user-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="72" @required(! $editing) @error('password_confirmation', 'userDetails') aria-invalid="true" aria-describedby="user-password-confirmation-error" @enderror>
                        <p id="user-password-confirmation-error" class="admin-field-error" data-admin-field-error="password_confirmation" role="alert" @if (! $errors->userDetails->has('password_confirmation')) hidden @endif>{{ $errors->userDetails->first('password_confirmation') }}</p>
                    </div>
                </div>
            </fieldset>
            <div class="admin-setting-actions admin-user-form-actions"><a class="admin-user-edit" href="{{ route('admin.users.index') }}">Cancel</a><button class="admin-button" type="submit">{{ $editing ? 'Save Changes' : 'Create User' }}</button></div>
        </form>
    </main>
</x-admin-shell>
