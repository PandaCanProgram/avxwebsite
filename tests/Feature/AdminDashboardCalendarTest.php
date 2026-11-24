<?php

namespace Tests\Feature;

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminDashboardCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-11-24 04:00:00', 'UTC'));
    }

    public function test_calendar_is_admin_only(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_dashboard_opens_the_current_month_and_today_with_an_empty_state(): void
    {
        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Job Order Calendar')->assertSee('November 2026')->assertSee('No starts or deadlines')
            ->assertSee('aria-current="date"', false)->assertSee('Your Account')
            ->assertViewHas('calendarSelectedDay', fn ($date): bool => $date->toDateString() === '2026-11-24')
            ->assertViewHas('calendarTotals', ['start' => 0, 'due' => 0]);
    }

    public function test_calendar_shows_starts_deadlines_and_assignees_on_the_selected_day(): void
    {
        $started = JobOrder::factory()->withAssignees(3)->create([
            'title' => 'Build flight software', 'created_at' => '2026-11-23 18:00:00', 'due_date' => '2026-11-30',
        ]);
        $due = JobOrder::factory()->create([
            'title' => 'Deliver drone chassis', 'created_at' => '2026-10-01 02:00:00', 'due_date' => '2026-11-24',
        ]);
        $noDeadline = JobOrder::factory()->create([
            'title' => 'Research next project', 'created_at' => '2026-11-24 06:00:00', 'due_date' => null,
        ]);
        $outside = JobOrder::factory()->create(['title' => 'Later project', 'created_at' => '2027-03-01 00:00:00', 'due_date' => '2027-03-20']);
        $response = $this->actingAs($started->creator)->get(route('admin.dashboard', ['month' => '2026-11', 'day' => '2026-11-24']))
            ->assertOk()->assertSee($started->title)->assertSee($due->title)->assertSee($noDeadline->title)
            ->assertSee('No due date set')->assertDontSee($outside->title)
            ->assertSee(route('admin.job-orders.edit', $started), false)
            ->assertViewHas('calendarTotals', ['start' => 2, 'due' => 2]);
        foreach ($started->assignees as $member) {
            $response->assertSee($member->name);
        }
        $events = collect($response->viewData('calendarEvents'));
        $this->assertEqualsCanonicalizing([$started->id, $due->id, $noDeadline->id], $events->pluck('job.id')->all());
        $this->assertSame(['due'], $events->where('job.id', $due->id)->pluck('kind')->values()->all());
        $day = collect($response->viewData('calendarWeeks'))->flatten(1)->first(fn (array $day): bool => $day['date']->toDateString() === '2026-11-24');
        $this->assertSame(2, $day['starts']);
        $this->assertSame(1, $day['due']);
    }

    public function test_start_and_due_on_the_same_day_are_both_visible(): void
    {
        $job = JobOrder::factory()->create(['created_at' => '2026-11-24 00:00:00', 'due_date' => '2026-11-24']);
        $response = $this->actingAs($job->creator)->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('calendarTotals', ['start' => 1, 'due' => 1]);
        $this->assertSame(['start', 'due'], array_column($response->viewData('calendarEvents'), 'kind'));
    }

    public function test_starts_use_philippine_dates_and_due_dates_do_not_shift(): void
    {
        $beforeMidnight = JobOrder::factory()->create(['created_at' => '2026-11-23 15:59:59', 'due_date' => '2026-11-24']);
        $atMidnight = JobOrder::factory()->create(['created_at' => '2026-11-23 16:00:00', 'due_date' => null]);
        $this->actingAs($atMidnight->creator);
        $response = $this->get(route('admin.dashboard', ['month' => '2026-11', 'day' => '2026-11-24']))->assertOk();
        $events = collect($response->viewData('calendarEvents'));
        $this->assertSame(['due'], $events->where('job.id', $beforeMidnight->id)->pluck('kind')->values()->all());
        $this->assertSame(['start'], $events->where('job.id', $atMidnight->id)->pluck('kind')->values()->all());
    }

    public function test_calendar_includes_visible_adjacent_days_but_month_totals_exclude_them(): void
    {
        $first = JobOrder::factory()->create(['created_at' => '2026-11-28 16:00:00', 'due_date' => null]);
        JobOrder::factory()->create(['created_at' => '2027-01-02 15:59:59', 'due_date' => '2027-01-02']);
        JobOrder::factory()->create(['created_at' => '2026-11-28 15:59:59', 'due_date' => '2027-01-03']);
        $response = $this->actingAs($first->creator)->get(route('admin.dashboard', ['month' => '2026-12']))->assertOk()
            ->assertViewHas('calendarTotals', ['start' => 0, 'due' => 0]);
        $days = collect($response->viewData('calendarWeeks'))->flatten(1);
        $this->assertSame('2026-11-29', $days->first()['date']->toDateString());
        $this->assertSame('2027-01-02', $days->last()['date']->toDateString());
        $this->assertSame(1, $days->first()['starts']);
        $this->assertSame(1, $days->last()['starts']);
        $this->assertSame(1, $days->last()['due']);
    }

    public function test_month_navigation_crosses_years_and_selects_the_first_day(): void
    {
        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.dashboard', ['month' => '2026-12']))->assertOk()
            ->assertSee(route('admin.dashboard', ['month' => '2027-01']), false)
            ->assertSee(route('admin.dashboard', ['month' => '2026-11']), false)
            ->assertViewHas('calendarSelectedDay', fn ($date): bool => $date->toDateString() === '2026-12-01');
    }

    public function test_leap_day_is_available_and_can_be_selected(): void
    {
        $job = JobOrder::factory()->create(['created_at' => '2028-02-01 00:00:00', 'due_date' => '2028-02-29']);
        $this->actingAs($job->creator)->get(route('admin.dashboard', ['month' => '2028-02', 'day' => '2028-02-29']))->assertOk()
            ->assertSee($job->title)->assertSee('Tuesday, February 29')
            ->assertViewHas('calendarSelectedDay', fn ($date): bool => $date->toDateString() === '2028-02-29');
    }

    #[TestWith(['invalid', '2026-11-24', '2026-11-24'])]
    #[TestWith([['2026-12'], ['2026-12-01'], '2026-11-24'])]
    #[TestWith(['2026-13', null, '2026-11-24'])]
    #[TestWith(['2026-02', '2026-02-30', '2026-02-01'])]
    #[TestWith(['2026-12', '2026-11-24', '2026-12-01'])]
    public function test_invalid_calendar_filters_fall_back_safely(mixed $month, mixed $day, string $selected): void
    {
        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.dashboard', compact('month', 'day')))->assertOk()
            ->assertViewHas('calendarSelectedDay', fn ($date): bool => $date->toDateString() === $selected);
    }

    public function test_job_titles_and_assignee_names_are_escaped(): void
    {
        $job = JobOrder::factory()->create(['title' => '<script>bad()</script>', 'due_date' => null]);
        $member = User::factory()->create(['name' => '<img src=x onerror=bad()>']);
        $job->assignees()->attach($member);
        $this->actingAs($job->creator)->get(route('admin.dashboard'))->assertOk()
            ->assertSee($job->title)->assertDontSee($job->title, false)
            ->assertSee($member->name)->assertDontSee($member->name, false);
    }
}
