<x-admin-shell title="Job Orders">
    <main id="main-content" class="admin-dashboard">
        <div class="admin-users-heading">
            <div><p class="admin-eyebrow">Team Assignments</p><h1>Job Orders</h1><p class="admin-dashboard-intro">Create work orders and assign the right people to handle them.</p></div>
            <a href="{{ route('admin.job-orders.create') }}" class="admin-button admin-add-user"><span aria-hidden="true">+</span> Create Job Order</a>
        </div>
        @if (session('jobOrderStatus'))
            <p class="admin-notice" role="status">{{ session('jobOrderStatus') }}</p>
        @endif

        <section class="admin-users-panel" aria-labelledby="job-orders-title">
            <div class="admin-users-panel-heading"><h2 id="job-orders-title">Work Orders</h2><span class="admin-users-count">{{ $jobOrders->total() }} {{ Str::plural('job', $jobOrders->total()) }}</span></div>
            @if ($jobOrders->isEmpty())
                <div class="admin-users-empty">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2M9 9h6m-6 4h6m-6 4h4"/></svg>
                    <h3>No Job Orders Yet</h3><p>Create your first job order and choose the team members who will handle it.</p>
                    <a href="{{ route('admin.job-orders.create') }}" class="admin-text-link">Create a Job Order <span aria-hidden="true">&rarr;</span></a>
                </div>
            @else
                <table class="admin-users-table admin-jobs-table" role="table">
                    <caption class="admin-sr-only">Job orders and their assigned users</caption>
                    <thead role="rowgroup"><tr role="row"><th scope="col" role="columnheader">Job Order</th><th scope="col" role="columnheader">Assigned To</th><th scope="col" role="columnheader">Due Date</th><th scope="col" role="columnheader">Actions</th></tr></thead>
                    <tbody role="rowgroup">
                        @foreach ($jobOrders as $jobOrder)
                            <tr role="row">
                                <td data-label="Job Order" role="cell"><div class="admin-job-title"><span>{{ $jobOrder->reference() }}</span><strong>{{ $jobOrder->title }}</strong><p>{{ Str::limit($jobOrder->description, 100) }}</p></div></td>
                                <td data-label="Assigned To" role="cell"><div class="admin-job-assignee-list">@foreach ($jobOrder->assignees as $assignee)<span class="admin-position-badge">{{ $assignee->name }}@if ($assignee->position)<small>{{ $assignee->position }}</small>@endif</span>@endforeach</div></td>
                                <td data-label="Due Date" role="cell">{{ $jobOrder->due_date?->format('M j, Y') ?? 'No due date' }}</td>
                                <td data-label="Actions" role="cell"><a class="admin-user-edit" href="{{ route('admin.job-orders.edit', $jobOrder) }}" aria-label="Edit {{ $jobOrder->reference() }}">Edit <span aria-hidden="true">&rarr;</span></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($jobOrders->hasPages())
                    <nav class="admin-user-pagination" aria-label="Job order pages">
                        <p>Showing {{ $jobOrders->firstItem() }}-{{ $jobOrders->lastItem() }} of {{ $jobOrders->total() }}</p>
                        <div>
                            @if ($jobOrders->onFirstPage())<span aria-disabled="true">Previous</span>@else<a href="{{ $jobOrders->previousPageUrl() }}" rel="prev">Previous</a>@endif
                            @if ($jobOrders->hasMorePages())<a href="{{ $jobOrders->nextPageUrl() }}" rel="next">Next</a>@else<span aria-disabled="true">Next</span>@endif
                        </div>
                    </nav>
                @endif
            @endif
        </section>
    </main>
</x-admin-shell>
