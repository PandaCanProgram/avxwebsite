<?php

namespace Tests\Feature;

use App\Models\JobOrder;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminJobOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @param list<int|string> $assignees
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function details(array $assignees, array $overrides = []): array
    {
        return array_merge([
            'title' => 'Build the project dashboard',
            'description' => 'Implement the approved design and review it with the team.',
            'due_date' => '2026-12-01',
            'assignees' => $assignees,
        ], $overrides);
    }

    #[TestWith(['get', 'admin.job-orders.index', false])]
    #[TestWith(['get', 'admin.job-orders.create', false])]
    #[TestWith(['post', 'admin.job-orders.store', false])]
    #[TestWith(['get', 'admin.job-orders.edit', true])]
    #[TestWith(['patch', 'admin.job-orders.update', true])]
    public function test_only_admins_can_access_job_orders(string $method, string $route, bool $needsOrder): void
    {
        $jobOrder = $needsOrder ? JobOrder::factory()->withAssignees()->create() : null;
        $url = route($route, $jobOrder ?? []);
        $this->{$method}($url)->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->{$method}($url)->assertForbidden();
        $this->assertDatabaseCount('job_orders', $needsOrder ? 1 : 0);
    }

    #[TestWith([1])]
    #[TestWith([2])]
    #[TestWith([5])]
    public function test_jobs_can_be_created_with_one_or_many_assignees(int $count): void
    {
        $admin = User::factory()->administrator()->create();
        $members = User::factory()->count($count)->create(['position' => 'Software']);
        $this->actingAs($admin)->post(route('admin.job-orders.store'), $this->details($members->modelKeys()))
            ->assertRedirect(route('admin.job-orders.index'))->assertSessionHas('jobOrderStatus');

        $jobOrder = JobOrder::sole();
        $this->assertSame($admin->id, $jobOrder->created_by);
        $this->assertSame('Build the project dashboard', $jobOrder->title);
        $this->assertSame('2026-12-01', $jobOrder->due_date->format('Y-m-d'));
        $this->assertEqualsCanonicalizing($members->modelKeys(), $jobOrder->assignees->modelKeys());
        $this->assertDatabaseCount('job_order_user', $count);
        $this->assertMatchesRegularExpression('/^JO-[0-9]{6,}$/', $jobOrder->reference());
        $this->assertSame($jobOrder->id, $members->first()->assignedJobOrders()->sole()->id);
    }

    public function test_a_single_user_can_be_chosen_and_due_date_is_optional(): void
    {
        $member = User::factory()->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details([$member->id, ''], ['due_date' => '']))
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.job-orders.index'));
        $this->assertNull(JobOrder::sole()->due_date);
        $this->assertDatabaseCount('job_order_user', 1);
    }

    public function test_at_least_one_assignee_is_required(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details([]))
            ->assertSessionHasErrorsIn('jobOrder', ['assignees']);
        $this->assertDatabaseCount('job_orders', 0);
        $this->assertDatabaseCount('job_order_user', 0);
    }

    public function test_duplicate_assignees_are_rejected(): void
    {
        $member = User::factory()->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details([$member->id, $member->id]))
            ->assertSessionHasErrorsIn('jobOrder', ['assignees.0']);
        $this->assertDatabaseCount('job_orders', 0);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_assignees_must_be_existing_team_members(bool $useAdmin): void
    {
        $admin = User::factory()->administrator()->create();
        $this->actingAs($admin)->post(route('admin.job-orders.store'), $this->details([$useAdmin ? $admin->id : 999999]))
            ->assertSessionHasErrorsIn('jobOrder', ['assignees.0']);
        $this->assertDatabaseCount('job_orders', 0);
    }

    #[TestWith(['title', ''])]
    #[TestWith(['description', ''])]
    #[TestWith(['due_date', 'not-a-date'])]
    public function test_invalid_job_details_are_rejected(string $field, string $value): void
    {
        $member = User::factory()->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details([$member->id], [$field => $value]))
            ->assertRedirect(route('admin.job-orders.create'))->assertSessionHasErrorsIn('jobOrder', [$field]);
        $this->assertDatabaseCount('job_orders', 0);
    }

    public function test_invalid_assignment_input_can_be_displayed_safely_again(): void
    {
        User::factory()->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details([], ['assignees' => [['invalid']]]))
            ->assertSessionHasErrorsIn('jobOrder', ['assignees.0']);
        $this->get(route('admin.job-orders.create'))->assertOk();
        $this->assertDatabaseCount('job_orders', 0);
    }

    public function test_empty_jobs_and_no_users_show_useful_guidance(): void
    {
        $this->actingAs(User::factory()->administrator()->create());
        $this->get(route('admin.job-orders.index'))->assertOk()->assertSee('No Job Orders Yet');
        $this->get(route('admin.job-orders.create'))->assertOk()->assertSee('Add a user in User Roles')
            ->assertSee(route('admin.users.create'), false);
    }

    public function test_participant_picker_only_offers_team_accounts_and_restores_saved_assignments(): void
    {
        $admin = User::factory()->administrator()->create();
        $members = User::factory()->count(4)->create(['position' => 'Hardware']);
        $unselectedMember = User::factory()->create(['position' => 'Software']);
        $jobOrder = JobOrder::factory()->for($admin, 'creator')->hasAttached($members, [], 'assignees')->create();
        $response = $this->actingAs($admin)->get(route('admin.job-orders.edit', $jobOrder))->assertOk();
        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $offered = [];
        $selected = [];
        foreach ($xpath->query('//input[@data-job-participant and @role="switch"]') as $input) {
            $offered[] = (int) $input->getAttribute('value');
            if ($input->hasAttribute('checked')) {
                $selected[] = (int) $input->getAttribute('value');
            }
        }
        $this->assertEqualsCanonicalizing([...$members->modelKeys(), $unselectedMember->id], $offered);
        $this->assertEqualsCanonicalizing($members->modelKeys(), $selected);
        $this->assertNotContains($admin->id, $offered);
        $this->assertSame(0, $xpath->query('//select[@name="assignees[]"]')->length);
        $this->assertSame(1, $xpath->query('//details[@data-job-participant-picker]/summary')->length);
        $response->assertSee('Choose Users');
    }

    public function test_participant_checkbox_payload_ignores_the_empty_hidden_field(): void
    {
        $members = User::factory()->count(3)->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.job-orders.store'), $this->details(['', ...$members->modelKeys()]))
            ->assertRedirect(route('admin.job-orders.index'))->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing($members->modelKeys(), JobOrder::sole()->assignees->modelKeys());
    }

    public function test_job_list_displays_reference_assignees_and_due_date(): void
    {
        $jobOrder = JobOrder::factory()->withAssignees(2)->create(['due_date' => '2026-12-01']);
        $response = $this->actingAs($jobOrder->creator)->get(route('admin.job-orders.index'))->assertOk()
            ->assertSee($jobOrder->reference())->assertSee($jobOrder->title)->assertSee('Dec 1, 2026');
        foreach ($jobOrder->assignees as $member) {
            $response->assertSee($member->name)->assertDontSee($member->password, false);
        }
    }

    public function test_reassignment_replaces_old_users_and_can_reduce_back_to_one(): void
    {
        $jobOrder = JobOrder::factory()->withAssignees()->create();
        $reference = $jobOrder->reference();
        $originalCreator = $jobOrder->created_by;
        $members = User::factory()->count(5)->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.job-orders.update', $jobOrder), $this->details($members->modelKeys()))
            ->assertRedirect(route('admin.job-orders.index'))->assertSessionHas('jobOrderStatus');
        $this->assertEqualsCanonicalizing($members->modelKeys(), $jobOrder->fresh()->assignees->modelKeys());
        $this->assertSame($reference, $jobOrder->fresh()->reference());
        $this->assertSame($originalCreator, $jobOrder->fresh()->created_by);
        $this->patch(route('admin.job-orders.update', $jobOrder), $this->details([$members->first()->id], ['due_date' => null]))
            ->assertSessionHasNoErrors();
        $this->assertSame([$members->first()->id], $jobOrder->fresh()->assignees->modelKeys());
        $this->assertNull($jobOrder->fresh()->due_date);
        $this->assertDatabaseCount('job_orders', 1);
        $this->assertDatabaseCount('job_order_user', 1);
    }

    public function test_invalid_reassignment_preserves_the_previous_job_and_assignments(): void
    {
        $jobOrder = JobOrder::factory()->withAssignees()->create();
        $previousTitle = $jobOrder->title;
        $previousAssignees = $jobOrder->assignees->modelKeys();
        $this->actingAs($jobOrder->creator)->patch(route('admin.job-orders.update', $jobOrder), $this->details([]))
            ->assertRedirect(route('admin.job-orders.edit', $jobOrder))->assertSessionHasErrorsIn('jobOrder', ['assignees']);
        $this->assertSame($previousTitle, $jobOrder->fresh()->title);
        $this->assertSame($previousAssignees, $jobOrder->fresh()->assignees->modelKeys());
    }

    public function test_the_creator_cannot_be_spoofed_by_the_request(): void
    {
        $admin = User::factory()->administrator()->create();
        $member = User::factory()->create();
        $this->actingAs($admin)->post(route('admin.job-orders.store'), $this->details([$member->id], ['created_by' => $member->id]))
            ->assertSessionHasErrorsIn('jobOrder', ['created_by']);
        $this->assertDatabaseCount('job_orders', 0);
    }

    public function test_the_list_is_paginated_with_newest_jobs_first(): void
    {
        $admin = User::factory()->administrator()->create();
        $member = User::factory()->create();
        $jobs = JobOrder::factory()->count(16)->for($admin, 'creator')->hasAttached($member, [], 'assignees')->create();
        $this->actingAs($admin)->get(route('admin.job-orders.index'))->assertOk()
            ->assertSee($jobs->last()->reference())->assertDontSee($jobs->first()->reference())->assertSee('Next');
        $this->get(route('admin.job-orders.index', ['page' => 2]))->assertOk()
            ->assertSee($jobs->first()->reference())->assertDontSee($jobs->last()->reference())->assertSee('Previous');
    }
}
