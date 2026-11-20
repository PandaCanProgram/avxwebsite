<?php

namespace Tests\Feature;

use App\Models\GroupMessage;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminMessagingTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['get', 'admin.messages.index', false])]
    #[TestWith(['get', 'admin.messages.create', false])]
    #[TestWith(['post', 'admin.messages.store', false])]
    #[TestWith(['get', 'admin.messages.show', true])]
    #[TestWith(['get', 'admin.messages.edit', true])]
    #[TestWith(['patch', 'admin.messages.update', true])]
    #[TestWith(['get', 'admin.messages.feed', true])]
    #[TestWith(['post', 'admin.messages.send', true])]
    public function test_messaging_requires_an_admin_session(string $method, string $route, bool $needsGroup): void
    {
        $group = $needsGroup ? MessageGroup::factory()->create() : null;
        $url = route($route, $group ?? []);
        $this->{$method}($url)->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->{$method}($url)->assertForbidden();
        $this->assertDatabaseCount('group_messages', 0);
    }

    public function test_admin_can_create_a_group_with_many_members_and_is_always_included(): void
    {
        $admin = User::factory()->administrator()->create();
        $members = User::factory()->count(5)->create(['position' => 'Software']);
        $this->actingAs($admin)->post(route('admin.messages.store'), [
            'name' => 'Software team', 'members' => [...$members->modelKeys(), ''],
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.messages.show', MessageGroup::sole()));

        $group = MessageGroup::sole();
        $this->assertSame($admin->id, $group->created_by);
        $this->assertSame('Software team', $group->name);
        $this->assertEqualsCanonicalizing([...$members->modelKeys(), $admin->id], $group->members->modelKeys());
        $this->assertSame($group->id, $members->first()->messageGroups()->sole()->id);
    }

    #[TestWith(['name', '', 'name'])]
    #[TestWith(['name', "Two\nlines", 'name'])]
    #[TestWith(['members', [], 'members'])]
    #[TestWith(['members', 'invalid', 'members'])]
    #[TestWith(['members', [999999], 'members.0'])]
    #[TestWith(['members', [['nested']], 'members.0'])]
    public function test_invalid_group_details_are_rejected(string $field, mixed $value, string $error): void
    {
        $member = User::factory()->create();
        $details = ['name' => 'Team chat', 'members' => [$member->id], $field => $value];
        $this->actingAs(User::factory()->administrator()->create())->post(route('admin.messages.store'), $details)
            ->assertRedirect(route('admin.messages.create'))->assertSessionHasErrorsIn('messageGroup', [$error]);
        $this->assertDatabaseCount('message_groups', 0);
    }

    public function test_members_must_be_distinct_team_accounts_and_owner_cannot_be_spoofed(): void
    {
        $admin = User::factory()->administrator()->create();
        $member = User::factory()->create();
        $this->actingAs($admin)->postJson(route('admin.messages.store'), [
            'name' => 'Team', 'members' => [$member->id, $member->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('members.0');
        $this->postJson(route('admin.messages.store'), [
            'name' => 'Team', 'members' => [$admin->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('members.0');
        $this->postJson(route('admin.messages.store'), [
            'name' => 'Team', 'members' => [$member->id], 'created_by' => $member->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('created_by');
        $this->assertDatabaseCount('message_groups', 0);
    }

    public function test_members_can_be_added_and_removed_without_losing_history_or_owner(): void
    {
        $group = MessageGroup::factory()->withMembers(3)->create();
        $oldMembers = $group->members->where('is_admin', false);
        $message = GroupMessage::factory()->for($group, 'group')->create();
        $newMembers = User::factory()->count(5)->create();
        $this->actingAs($group->creator)->patch(route('admin.messages.update', $group), [
            'name' => 'Renamed group', 'members' => $newMembers->modelKeys(),
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.messages.show', $group));
        $this->assertEqualsCanonicalizing([...$newMembers->modelKeys(), $group->created_by], $group->fresh()->members->modelKeys());
        $this->assertSame('Renamed group', $group->fresh()->name);
        $this->assertSame(0, $oldMembers->first()->messageGroups()->count());
        $this->assertModelExists($message);

        $this->patch(route('admin.messages.update', $group), ['name' => 'Renamed group', 'members' => ['']])
            ->assertSessionHasNoErrors();
        $this->assertSame([$group->created_by], $group->fresh()->members->modelKeys());
        $this->assertModelExists($message);
    }

    #[TestWith(['get', 'admin.messages.show'])]
    #[TestWith(['get', 'admin.messages.edit'])]
    #[TestWith(['patch', 'admin.messages.update'])]
    #[TestWith(['post', 'admin.messages.send'])]
    #[TestWith(['get', 'admin.messages.feed'])]
    public function test_another_admin_cannot_read_or_modify_a_private_group(string $method, string $route): void
    {
        $group = MessageGroup::factory()->withMembers()->create();
        $this->actingAs(User::factory()->administrator()->create())->{$method}(route($route, $group))
            ->assertForbidden();
        $this->assertDatabaseCount('group_messages', 0);
        $this->assertDatabaseCount('message_group_user', 2);
    }

    public function test_messages_page_lists_only_owned_groups_and_has_member_controls(): void
    {
        $owned = MessageGroup::factory()->withMembers()->create(['name' => 'My team']);
        $foreign = MessageGroup::factory()->create(['name' => 'Private other team']);
        $this->actingAs($owned->creator)->get(route('admin.messages.index'))->assertOk()
            ->assertSee('My team')->assertDontSee($foreign->name)->assertSee('New Group')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get(route('admin.messages.show', $owned))->assertOk()->assertSee('Manage Members')
            ->assertSee('name="_token"', false)->assertSee('data-chat-composer', false);
        $this->get(route('admin.messages.edit', $owned))->assertOk()->assertSee('role="switch"', false)
            ->assertSee('name="members[]"', false)->assertSee('Save Changes');
        $this->get(route('admin.messages.create'))->assertOk()->assertSee('Create Group');
    }

    public function test_group_selection_is_paginated(): void
    {
        $admin = User::factory()->administrator()->create();
        MessageGroup::factory()->count(21)->for($admin, 'creator')->create();
        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertOk()->assertViewHas('groups', fn ($groups): bool => $groups->total() === 21 && $groups->count() === 20);
    }

    public function test_admin_can_send_and_retry_without_duplicate_messages(): void
    {
        $group = MessageGroup::factory()->withMembers()->create(['updated_at' => now()->subDay()]);
        $details = ['body' => "Project update\nReady for review.", 'client_id' => (string) Str::uuid()];
        $this->actingAs($group->creator);
        $response = $this->postJson(route('admin.messages.send', $group), $details)->assertOk();
        $this->assertTrue(Str::isUuid($response->json('next_client_id')));
        $this->assertNotSame($details['client_id'], $response->json('next_client_id'));
        $this->postJson(route('admin.messages.send', $group), $details)->assertOk()->assertJsonPath('id', $response->json('id'));
        $this->assertDatabaseCount('group_messages', 1);
        $message = GroupMessage::sole();
        $this->assertSame($group->created_by, $message->user_id);
        $this->assertSame($details['body'], $message->body);
        $this->assertSame($group->id, $message->message_group_id);
        $this->assertTrue($group->fresh()->updated_at->isToday());
    }

    public function test_sending_works_with_a_normal_form_submission(): void
    {
        $group = MessageGroup::factory()->create();
        $this->actingAs($group->creator)->post(route('admin.messages.send', $group), [
            'body' => 'A message without JavaScript.', 'client_id' => (string) Str::uuid(),
        ])->assertRedirect(route('admin.messages.show', $group));
        $this->assertDatabaseCount('group_messages', 1);
    }

    #[TestWith(['body', '   '])]
    #[TestWith(['body', ['nested']])]
    #[TestWith(['client_id', 'not-a-uuid'])]
    #[TestWith(['user_id', 123])]
    #[TestWith(['message_group_id', 456])]
    public function test_invalid_messages_and_spoofed_identity_are_rejected(string $field, mixed $value): void
    {
        $group = MessageGroup::factory()->create();
        $this->actingAs($group->creator)->postJson(route('admin.messages.send', $group), [
            'body' => 'Hello', 'client_id' => (string) Str::uuid(), $field => $value,
        ])->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('group_messages', 0);
    }

    public function test_long_messages_are_rejected(): void
    {
        $group = MessageGroup::factory()->create();
        $this->actingAs($group->creator)->postJson(route('admin.messages.send', $group), [
            'body' => str_repeat('a', 5001), 'client_id' => (string) Str::uuid(),
        ])->assertUnprocessable()->assertJsonValidationErrors('body');
        $this->assertDatabaseCount('group_messages', 0);
    }

    public function test_names_and_messages_are_rendered_as_plain_text(): void
    {
        $group = MessageGroup::factory()->create(['name' => '<img src=x onerror=alert(1)>']);
        $body = '<script>alert("test")</script>';
        GroupMessage::factory()->for($group, 'group')->create(['body' => $body]);
        $this->actingAs($group->creator)->get(route('admin.messages.show', $group))->assertOk()
            ->assertSee($body)->assertDontSee($body, false)->assertSee($group->name)->assertDontSee($group->name, false);
        $feed = $this->getJson(route('admin.messages.feed', $group))->assertOk();
        $this->assertStringNotContainsString($body, $feed->json('html'));
        $this->assertStringContainsString(e($body), $feed->json('html'));
    }

    public function test_history_is_ordered_and_can_load_older_and_new_messages_without_leaking_other_groups(): void
    {
        $group = MessageGroup::factory()->create();
        $messages = GroupMessage::factory()->count(53)->for($group, 'group')->create();
        $foreign = GroupMessage::factory()->create(['body' => 'Foreign secret message']);
        $this->actingAs($group->creator)->get(route('admin.messages.show', $group))->assertOk()
            ->assertViewHas('messages', fn ($items): bool => $items->count() === 50 && $items->first()->id === $messages[3]->id)
            ->assertViewHas('hasEarlier', true)->assertDontSee($foreign->body);
        $older = $this->getJson(route('admin.messages.feed', [$group, 'before' => $messages[3]->id]))->assertOk()
            ->assertJsonPath('first_id', $messages[0]->id)->assertJsonPath('last_id', $messages[2]->id)
            ->assertJsonPath('has_more', false);
        $this->assertStringNotContainsString($foreign->body, $older->json('html'));
        $newer = $this->getJson(route('admin.messages.feed', [$group, 'after' => $messages[50]->id]))->assertOk()
            ->assertJsonPath('first_id', $messages[51]->id)->assertJsonPath('last_id', $messages[52]->id);
        $this->assertStringNotContainsString($foreign->body, $newer->json('html'));
        $this->getJson(route('admin.messages.feed', [$group, 'after' => $messages->last()->id]))->assertOk()
            ->assertJsonPath('html', '')->assertJsonPath('last_id', null);
        $this->getJson(route('admin.messages.feed', [$group, 'before' => 0]))->assertUnprocessable();
        $this->getJson(route('admin.messages.feed', [$group, 'before' => 1, 'after' => 0]))->assertUnprocessable();
    }
}
