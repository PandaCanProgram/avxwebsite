<?php

namespace Tests\Feature;

use App\Models\GroupMessage;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminDirectMessageTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['get', 'admin.messages.direct.create'])]
    #[TestWith(['post', 'admin.messages.direct.store'])]
    public function test_starting_a_private_chat_requires_an_admin(string $method, string $route): void
    {
        $this->{$method}(route($route))->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->{$method}(route($route))->assertForbidden();
        $this->assertDatabaseCount('message_groups', 0);
    }

    public function test_admin_can_open_a_private_conversation_with_exactly_one_user(): void
    {
        $admin = User::factory()->administrator()->create();
        $recipient = User::factory()->create(['name' => 'Software Member', 'position' => 'Software']);
        $this->actingAs($admin)->get(route('admin.messages.index'))->assertOk()
            ->assertSee(route('admin.messages.direct.create'), false)->assertSee('New Group');
        $this->get(route('admin.messages.direct.create'))->assertOk()->assertSee($recipient->name)
            ->assertSee('name="recipient_id"', false)->assertDontSee('name="members[]"', false);
        $this->post(route('admin.messages.direct.store'), ['recipient_id' => $recipient->id])
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.messages.show', MessageGroup::sole()));

        $conversation = MessageGroup::sole();
        $this->assertTrue($conversation->isDirect());
        $this->assertSame($admin->id, $conversation->created_by);
        $this->assertSame($recipient->id, $conversation->direct_user_id);
        $this->assertEqualsCanonicalizing([$admin->id, $recipient->id], $conversation->members->modelKeys());
        $this->get(route('admin.messages.show', $conversation))->assertOk()->assertSee('Private Message')
            ->assertSee('Message '.$recipient->name)->assertDontSee('Manage Members')->assertDontSee('View Members');
    }

    public function test_reopening_a_private_chat_reuses_its_history_and_does_not_reuse_a_group(): void
    {
        $group = MessageGroup::factory()->withMembers()->create();
        $admin = $group->creator;
        $recipient = $group->members->firstWhere('is_admin', false);
        $this->actingAs($admin)->post(route('admin.messages.direct.store'), ['recipient_id' => $recipient->id]);
        $direct = MessageGroup::whereNotNull('direct_user_id')->sole();
        $message = GroupMessage::factory()->for($direct, 'group')->create(['body' => 'A saved private message']);
        $this->post(route('admin.messages.direct.store'), ['recipient_id' => $recipient->id])
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.messages.show', $direct));

        $this->assertDatabaseCount('message_groups', 2);
        $this->assertFalse($group->fresh()->isDirect());
        $this->assertModelExists($message);
        $this->get(route('admin.messages.show', $direct))->assertOk()->assertSee($message->body);
    }

    public function test_private_chats_for_different_recipients_remain_separate(): void
    {
        $admin = User::factory()->administrator()->create();
        $recipients = User::factory()->count(2)->create();
        $this->actingAs($admin);
        foreach ($recipients as $recipient) {
            $this->post(route('admin.messages.direct.store'), ['recipient_id' => $recipient->id])->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('message_groups', 2);
        $this->assertDatabaseCount('message_group_user', 4);
    }

    #[TestWith([null])]
    #[TestWith([''])]
    #[TestWith(['not-a-user'])]
    #[TestWith([999999])]
    #[TestWith([[1, 2]])]
    public function test_invalid_or_multiple_recipients_are_rejected(mixed $recipient): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.messages.direct.store'), ['recipient_id' => $recipient])
            ->assertRedirect(route('admin.messages.direct.create'))->assertSessionHasErrorsIn('directMessage', 'recipient_id');
        $this->assertDatabaseCount('message_groups', 0);
    }

    public function test_self_admin_accounts_and_extra_members_cannot_be_selected(): void
    {
        $admin = User::factory()->administrator()->create();
        $otherAdmin = User::factory()->administrator()->create();
        $member = User::factory()->create();
        $this->actingAs($admin);
        foreach ([$admin, $otherAdmin] as $recipient) {
            $this->postJson(route('admin.messages.direct.store'), ['recipient_id' => $recipient->id])
                ->assertUnprocessable()->assertJsonValidationErrors('recipient_id');
        }
        $this->postJson(route('admin.messages.direct.store'), ['recipient_id' => $member->id, 'members' => [$otherAdmin->id]])
            ->assertUnprocessable()->assertJsonValidationErrors('members');
        $this->postJson(route('admin.messages.direct.store'), ['recipient_id' => $member->id, 'created_by' => $otherAdmin->id])
            ->assertUnprocessable()->assertJsonValidationErrors('created_by');
        $this->assertDatabaseCount('message_groups', 0);
    }

    public function test_a_private_chat_cannot_be_renamed_or_have_its_participants_changed(): void
    {
        $direct = MessageGroup::factory()->direct()->create();
        $extra = User::factory()->create();
        $originalMembers = $direct->members->modelKeys();
        $this->actingAs($direct->creator)->get(route('admin.messages.edit', $direct))->assertForbidden();
        $this->patch(route('admin.messages.update', $direct), ['name' => 'Renamed', 'members' => [$extra->id]])
            ->assertForbidden();
        $this->assertEqualsCanonicalizing($originalMembers, $direct->fresh()->members->modelKeys());
        $this->assertSame('Private conversation', $direct->fresh()->name);
    }

    #[TestWith(['get', 'admin.messages.show'])]
    #[TestWith(['get', 'admin.messages.feed'])]
    #[TestWith(['post', 'admin.messages.send'])]
    public function test_other_admins_cannot_access_a_private_chat(string $method, string $route): void
    {
        $direct = MessageGroup::factory()->direct()->create();
        $this->actingAs(User::factory()->administrator()->create())->{$method}(route($route, $direct))->assertForbidden();
        $this->assertDatabaseCount('group_messages', 0);
    }

    public function test_private_messages_are_saved_only_to_the_selected_conversation(): void
    {
        $direct = MessageGroup::factory()->direct()->create();
        $other = MessageGroup::factory()->direct()->for($direct->creator, 'creator')->create();
        $this->actingAs($direct->creator)->postJson(route('admin.messages.send', $direct), [
            'body' => 'Private project update', 'client_id' => (string) Str::uuid(),
        ])->assertOk();
        $this->assertSame(1, $direct->messages()->count());
        $this->assertSame(0, $other->messages()->count());
        $this->getJson(route('admin.messages.feed', $other))->assertOk()->assertJsonPath('html', '');
        $this->getJson(route('admin.messages.feed', $direct))->assertOk()->assertSee('Private project update');
    }

    public function test_recipient_names_follow_profile_updates_and_are_escaped(): void
    {
        $direct = MessageGroup::factory()->direct()->create();
        $direct->recipient->update(['name' => '<script>New name</script>']);
        $this->actingAs($direct->creator)->get(route('admin.messages.show', $direct))->assertOk()
            ->assertSee('<script>New name</script>')->assertDontSee('<script>New name</script>', false);
    }

    public function test_no_users_has_an_add_user_link_and_disabled_submit(): void
    {
        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.messages.direct.create'))->assertOk()
            ->assertSee(route('admin.users.create'), false)->assertSee('disabled', false);
    }
}
