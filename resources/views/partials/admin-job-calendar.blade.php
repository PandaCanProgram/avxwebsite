<section class="admin-calendar" id="job-calendar" aria-labelledby="job-calendar-title">
    <div class="admin-calendar-heading">
        <div><p class="admin-eyebrow">Your Work at a Glance</p><h2 id="job-calendar-title">Job Order Calendar</h2><p>Keep track of job starts and upcoming deadlines.</p></div>
        <a class="admin-user-edit" href="{{ route('admin.job-orders.index') }}">View Job Orders &rarr;</a>
    </div>
    <div class="admin-calendar-layout">
        <div class="admin-calendar-month">
            <div class="admin-calendar-toolbar">
                <h3>{{ $calendarMonth->format('F Y') }}</h3>
                <nav aria-label="Calendar month">
                    <a class="admin-user-edit" href="{{ route('admin.dashboard') }}#job-calendar">Today</a>
                    @if ($calendarMonth->year > 1000 || $calendarMonth->month > 1)<a class="admin-user-edit admin-calendar-arrow" href="{{ route('admin.dashboard', ['month' => $calendarMonth->subMonth()->format('Y-m')]) }}#job-calendar" aria-label="Previous month, {{ $calendarMonth->subMonth()->format('F Y') }}">&lsaquo;</a>@endif
                    @if ($calendarMonth->year < 9998 || $calendarMonth->month < 12)<a class="admin-user-edit admin-calendar-arrow" href="{{ route('admin.dashboard', ['month' => $calendarMonth->addMonth()->format('Y-m')]) }}#job-calendar" aria-label="Next month, {{ $calendarMonth->addMonth()->format('F Y') }}">&rsaquo;</a>@endif
                </nav>
            </div>
            <div class="admin-calendar-legend"><span class="admin-calendar-start">Starts <strong>{{ $calendarTotals['start'] }}</strong></span><span class="admin-calendar-due">Due dates <strong>{{ $calendarTotals['due'] }}</strong></span><span>This month</span></div>
            <table class="admin-calendar-grid">
                <caption class="admin-sr-only">{{ $calendarMonth->format('F Y') }} job order start dates and deadlines. Select a day to view its jobs.</caption>
                <thead><tr>@foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekday)<th scope="col"><abbr title="{{ $weekday }}">{{ substr($weekday, 0, 3) }}</abbr></th>@endforeach</tr></thead>
                <tbody>
                    @foreach ($calendarWeeks as $week)
                        <tr>
                            @foreach ($week as $day)
                                @php($date = $day['date'])
                                <td class="{{ $date->isSameMonth($calendarMonth) ? '' : 'admin-calendar-outside' }}">
                                    <a class="admin-calendar-day" href="{{ route('admin.dashboard', ['month' => $date->format('Y-m'), 'day' => $date->toDateString()]) }}#job-calendar" data-selected="{{ $date->isSameDay($calendarSelectedDay) ? 'true' : 'false' }}" aria-label="{{ $date->format('l, F j, Y') }}, {{ $day['starts'] }} {{ Str::plural('start', $day['starts']) }}, {{ $day['due'] }} due{{ $date->isSameDay($calendarSelectedDay) ? ', selected' : '' }}" @if ($date->isSameDay($calendarToday)) aria-current="date" @endif>
                                        <time datetime="{{ $date->toDateString() }}">{{ $date->day }}</time>
                                        <span class="admin-calendar-marks" aria-hidden="true">
                                            @if ($day['starts'])<span class="admin-calendar-mark admin-calendar-start"><span>Start</span><b>{{ $day['starts'] }}</b></span>@endif
                                            @if ($day['due'])<span class="admin-calendar-mark admin-calendar-due"><span>Due</span><b>{{ $day['due'] }}</b></span>@endif
                                        </span>
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="admin-calendar-footnote">Select a day to view its jobs. Start dates use the date the job order was created. Times use Philippine time.</p>
        </div>
        <aside class="admin-calendar-agenda" aria-labelledby="calendar-agenda-title">
            <p class="admin-eyebrow">Selected Day</p><h3 id="calendar-agenda-title">{{ $calendarSelectedDay->format('l, F j') }}</h3>
            <p class="admin-calendar-agenda-count">{{ count($calendarEvents) }} {{ Str::plural('event', count($calendarEvents)) }}</p>
            @forelse ($calendarEvents as $event)
                <article class="admin-calendar-job">
                    <span class="admin-calendar-event-type {{ $event['kind'] === 'start' ? 'admin-calendar-start' : 'admin-calendar-due' }}">{{ $event['kind'] === 'start' ? 'Started' : 'Due Date' }}</span>
                    <h4><a href="{{ route('admin.job-orders.edit', $event['job']) }}">{{ $event['job']->title }}</a></h4>
                    <p class="admin-calendar-reference">{{ $event['job']->reference() }}</p>
                    <dl><div><dt>Started</dt><dd>{{ $event['job']->created_at->copy()->timezone('Asia/Manila')->format('M j, Y') }}</dd></div><div><dt>Due</dt><dd>{{ $event['job']->due_date?->format('M j, Y') ?? 'No due date set' }}</dd></div></dl>
                    <p class="admin-calendar-assignees"><strong>Assigned to</strong> {{ $event['job']->assignees->pluck('name')->join(', ') ?: 'No users assigned' }}</p>
                    <a class="admin-text-link" href="{{ route('admin.job-orders.edit', $event['job']) }}">Open Job Order &rarr;</a>
                </article>
            @empty
                <div class="admin-calendar-empty"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6m10-6v6M3 11h18m-12 5 2 2 4-4"/></svg><h4>No starts or deadlines</h4><p>Choose a marked day to see job details, or create a new job order.</p><a class="admin-text-link" href="{{ route('admin.job-orders.create') }}">Create Job Order &rarr;</a></div>
            @endforelse
        </aside>
    </div>
</section>
