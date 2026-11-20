<x-admin-shell title="User Roles">
    <main id="main-content" class="admin-dashboard">
        <div class="admin-users-heading">
            <div><p class="admin-eyebrow">Your Team</p><h1>User Roles</h1><p class="admin-dashboard-intro">Create user accounts and manage their details and positions.</p></div>
            <a href="{{ route('admin.users.create') }}" class="admin-button admin-add-user"><span aria-hidden="true">+</span> Add User</a>
        </div>

        @if (session('userStatus'))
            <p class="admin-notice" role="status">{{ session('userStatus') }}</p>
        @endif

        <section class="admin-users-panel" aria-labelledby="users-list-title">
            <div class="admin-users-panel-heading"><h2 id="users-list-title">Team Members</h2><span class="admin-users-count">{{ $users->total() }} {{ Str::plural('user', $users->total()) }}</span></div>
            @if ($users->isEmpty())
                <div class="admin-users-empty">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="9" cy="7" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3m1-17a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 4v3"/></svg>
                    <h3>No Users Yet</h3><p>Add your first team member and assign their position.</p>
                    <a href="{{ route('admin.users.create') }}" class="admin-text-link">Add Your First User <span aria-hidden="true">&rarr;</span></a>
                </div>
            @else
                <table class="admin-users-table" role="table">
                    <caption class="admin-sr-only">User accounts and assigned positions</caption>
                    <thead role="rowgroup"><tr role="row"><th scope="col" role="columnheader">User</th><th scope="col" role="columnheader">Position / Role</th><th scope="col" role="columnheader">Actions</th></tr></thead>
                    <tbody role="rowgroup">
                        @foreach ($users as $member)
                            <tr role="row">
                                <td data-label="User" role="cell"><div class="admin-user-identity"><span class="admin-avatar" aria-hidden="true">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</span><div><strong>{{ $member->name }}</strong><span>{{ $member->email }}</span></div></div></td>
                                <td data-label="Position / Role" role="cell"><span class="admin-position-badge">{{ $member->position ?? 'Not assigned' }}</span></td>
                                <td data-label="Actions" role="cell"><a class="admin-user-edit" href="{{ route('admin.users.edit', $member) }}" aria-label="Edit {{ $member->name }}">Edit User <span aria-hidden="true">&rarr;</span></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($users->hasPages())
                    <nav class="admin-user-pagination" aria-label="User list pages">
                        <p>Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}</p>
                        <div>
                            @if ($users->onFirstPage())
                                <span aria-disabled="true">Previous</span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" rel="prev">Previous</a>
                            @endif
                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" rel="next">Next</a>
                            @else
                                <span aria-disabled="true">Next</span>
                            @endif
                        </div>
                    </nav>
                @endif
            @endif
        </section>
    </main>
</x-admin-shell>
