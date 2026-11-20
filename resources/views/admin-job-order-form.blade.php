@php
    $editing = $jobOrder !== null;
    $fieldValue = function (string $field) use ($jobOrder): string {
        $default = $field === 'due_date' ? $jobOrder?->due_date?->format('Y-m-d') : ($jobOrder?->{$field} ?? '');
        $value = old($field, $default);

        return is_string($value) ? $value : '';
    };
    $selectedAssignees = old('assignees', $jobOrder?->assignees->modelKeys() ?? []);
    $selectedAssignees = is_array($selectedAssignees) ? array_map('strval', array_filter($selectedAssignees, 'is_scalar')) : [];
    $selectedUsers = $users->filter(fn (App\Models\User $member): bool => in_array((string) $member->id, $selectedAssignees, true));
    $assignmentError = $errors->jobOrder->first('assignees') ?: $errors->jobOrder->first('assignees.*');
@endphp

<x-admin-shell :title="$editing ? 'Edit Job Order' : 'Create Job Order'">
    <main id="main-content" class="admin-dashboard">
        <a class="admin-text-link admin-users-back" href="{{ route('admin.job-orders.index') }}"><span aria-hidden="true">&larr;</span> Back to Job Orders</a>
        <p class="admin-eyebrow">Team Assignments @if ($editing)/ {{ $jobOrder->reference() }}@endif</p>
        <h1>{{ $editing ? 'Edit Job Order' : 'Create Job Order' }}</h1>
        <p class="admin-dashboard-intro">Describe the work and choose the users who will handle it.</p>
        @if ($users->isEmpty())
            <p class="admin-notice">Add a user in User Roles before creating a job order. <a class="admin-text-link" href="{{ route('admin.users.create') }}">Add User <span aria-hidden="true">&rarr;</span></a></p>
        @endif

        <form method="POST" action="{{ $editing ? route('admin.job-orders.update', $jobOrder) : route('admin.job-orders.store') }}" class="admin-user-form admin-setting-card">
            @csrf
            @if ($editing) @method('PATCH') @endif
            <fieldset class="admin-user-fieldset" @disabled($users->isEmpty())>
                <legend>Job Details</legend>
                <div class="admin-job-details-grid">
                    <div class="admin-field">
                        <label for="job-title">Job Title</label>
                        <input id="job-title" name="title" type="text" value="{{ $fieldValue('title') }}" maxlength="160" required @error('title', 'jobOrder') aria-invalid="true" aria-describedby="job-title-error" @enderror>
                        @error('title', 'jobOrder')<p id="job-title-error" class="admin-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div class="admin-field">
                        <label for="job-description">Instructions / Description</label>
                        <textarea id="job-description" name="description" rows="5" maxlength="5000" required @error('description', 'jobOrder') aria-invalid="true" aria-describedby="job-description-error" @enderror>{{ $fieldValue('description') }}</textarea>
                        @error('description', 'jobOrder')<p id="job-description-error" class="admin-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div class="admin-field admin-job-due-field">
                        <label for="job-due-date">Due Date <span class="admin-optional">(Optional)</span></label>
                        <input id="job-due-date" name="due_date" type="date" value="{{ $fieldValue('due_date') }}" @error('due_date', 'jobOrder') aria-invalid="true" aria-describedby="job-due-date-error" @enderror>
                        @error('due_date', 'jobOrder')<p id="job-due-date-error" class="admin-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>
            <fieldset class="admin-user-fieldset admin-user-password-section" data-job-assignees @disabled($users->isEmpty())>
                <legend>Assigned Users</legend>
                <p class="admin-field-help" id="job-assignees-help">Choose as many participants as needed. Toggle a user on to include them, or off to remove them.</p>
                <input type="hidden" name="assignees[]" value="">
                <p class="admin-field-error" id="job-assignees-error" data-job-participant-error role="alert" @if (! $assignmentError) hidden @endif>{{ $assignmentError }}</p>
                @if ($users->isNotEmpty())
                    <details class="admin-participant-picker" data-job-participant-picker @if ($assignmentError) open @endif>
                        <summary class="admin-participant-trigger" data-job-participant-trigger aria-controls="job-participant-list" aria-describedby="job-assignees-help{{ $assignmentError ? ' job-assignees-error' : '' }}" @if ($assignmentError) aria-invalid="true" @endif>
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="9" cy="7" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3m1-17a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 4v3"/></svg>
                            <span>Choose Users</span><span class="admin-participant-count" data-job-participant-count role="status" aria-live="polite" aria-atomic="true">{{ $selectedUsers->count() }} selected</span>
                            <svg class="admin-participant-chevron" viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </summary>
                        <div class="admin-participant-panel">
                            <div class="admin-participant-list" id="job-participant-list">
                                @foreach ($users as $member)
                                    <label class="admin-participant-choice" for="job-participant-{{ $member->id }}">
                                        <span class="admin-avatar" aria-hidden="true">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</span>
                                        <span class="admin-participant-identity"><strong>{{ $member->name }}</strong><span>{{ $member->position ?? 'Team Member' }}</span><small>{{ $member->email }}</small></span>
                                        <input id="job-participant-{{ $member->id }}" type="checkbox" name="assignees[]" value="{{ $member->id }}" role="switch" data-job-participant @checked(in_array((string) $member->id, $selectedAssignees, true))>
                                    </label>
                                @endforeach
                            </div>
                            <div class="admin-participant-footer"><p>Toggle users on or off to update participants.</p><button type="button" class="admin-user-edit" data-job-participant-done hidden>Done</button></div>
                        </div>
                    </details>
                @endif
                <div class="admin-job-selected-users">
                    @foreach ($users as $member)
                        <span class="admin-position-badge" data-job-selected-user="{{ $member->id }}" @if (! in_array((string) $member->id, $selectedAssignees, true)) hidden @endif>{{ $member->name }}</span>
                    @endforeach
                    <span class="admin-field-help" data-job-no-participants @if ($selectedUsers->isNotEmpty()) hidden @endif>No participants chosen yet.</span>
                </div>
            </fieldset>
            <div class="admin-setting-actions admin-user-form-actions"><a class="admin-user-edit" href="{{ route('admin.job-orders.index') }}">Cancel</a><button class="admin-button" type="submit" @disabled($users->isEmpty())>{{ $editing ? 'Save Changes' : 'Create Job Order' }}</button></div>
        </form>
    </main>
</x-admin-shell>
