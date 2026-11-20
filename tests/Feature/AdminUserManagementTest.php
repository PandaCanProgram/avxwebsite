<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function userDetails(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Team Member',
            'email' => 'member@example.test',
            'position' => 'Software',
            'password' => 'MemberPassword!2026',
            'password_confirmation' => 'MemberPassword!2026',
        ], $overrides);
    }

    #[TestWith(['get', 'admin.users.index', false])]
    #[TestWith(['get', 'admin.users.create', false])]
    #[TestWith(['post', 'admin.users.store', false])]
    #[TestWith(['get', 'admin.users.edit', true])]
    #[TestWith(['patch', 'admin.users.update', true])]
    public function test_only_administrators_can_manage_users(string $method, string $route, bool $needsUser): void
    {
        $member = User::factory()->create();
        $url = route($route, $needsUser ? $member : []);

        $this->{$method}($url)->assertRedirect(route('admin.login'));
        $this->actingAs($member)->{$method}($url)->assertForbidden();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_roles_lists_team_accounts_without_exposing_passwords_or_listing_administrators(): void
    {
        $admin = User::factory()->administrator()->create(['email' => 'owner@example.test']);
        $otherAdmin = User::factory()->administrator()->create(['email' => 'hidden-admin@example.test']);
        $member = User::factory()->create(['name' => 'Alex Member', 'position' => 'Electronics']);

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()
            ->assertSee('User Roles')->assertSee('Alex Member')->assertSee($member->email)
            ->assertSee('Electronics')->assertDontSee($member->password, false)
            ->assertDontSee($otherAdmin->email)->assertSee(route('admin.users.edit', $member), false);
    }

    public function test_an_empty_team_shows_an_add_user_action(): void
    {
        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.users.index'))
            ->assertOk()->assertSee('No Users Yet')->assertSee(route('admin.users.create'), false);
    }

    #[TestWith(['Software'])]
    #[TestWith(['Hardware'])]
    public function test_an_admin_can_create_a_user_with_an_allowed_position_and_a_hashed_password(string $position): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.users.store'), $this->userDetails([
                'email' => ' Member@Example.test ', 'position' => $position,
            ]))->assertRedirect(route('admin.users.index'))->assertSessionHas('userStatus');

        $member = User::where('email', 'member@example.test')->sole();
        $this->assertSame($position, $member->position);
        $this->assertFalse($member->is_admin);
        $this->assertTrue(Hash::check('MemberPassword!2026', $member->password));
        $this->assertNotSame('MemberPassword!2026', $member->password);
    }

    #[TestWith(['name', ''])]
    #[TestWith(['email', 'not-an-email'])]
    #[TestWith(['position', ''])]
    #[TestWith(['position', 'Programmer'])]
    #[TestWith(['password', 'short'])]
    #[TestWith(['password_confirmation', 'DifferentPassword!2026'])]
    public function test_invalid_account_details_are_rejected(string $field, string $value): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), $this->userDetails([$field => $value]))
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrorsIn('userDetails', [$field === 'password_confirmation' ? 'password' : $field])
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_duplicate_email_is_rejected_after_normalization_without_overwriting_the_existing_account(): void
    {
        $admin = User::factory()->administrator()->create(['email' => 'owner@example.test']);
        $originalHash = $admin->password;

        $this->actingAs($admin)->post(route('admin.users.store'), $this->userDetails(['email' => ' OWNER@EXAMPLE.TEST ']))
            ->assertSessionHasErrorsIn('userDetails', ['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertSame($originalHash, $admin->fresh()->password);
    }

    public function test_the_create_form_recovers_from_invalid_non_text_input(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.users.store'), $this->userDetails(['name' => ['invalid']]))
            ->assertRedirect(route('admin.users.create'))->assertSessionHasErrorsIn('userDetails', ['name']);

        $this->get(route('admin.users.create'))->assertOk()->assertSee('Create User');
    }

    public function test_edit_form_shows_the_users_details_without_their_password(): void
    {
        $member = User::factory()->create(['name' => 'Alex Member', 'position' => 'Editing']);

        $this->actingAs(User::factory()->administrator()->create())->get(route('admin.users.edit', $member))
            ->assertOk()->assertSee('Alex Member')->assertSee($member->email)->assertSee('Current saved position: Editing')
            ->assertSee('Leave both password fields blank')->assertDontSee($member->password, false);
    }

    #[TestWith([false, 'Abcd1234', true])]
    #[TestWith([false, 'Abcd123', false])]
    #[TestWith([true, 'Abcd1234', true])]
    #[TestWith([true, 'Abcd123', false])]
    public function test_password_minimum_is_eight_characters_for_creation_and_resets(bool $editing, string $password, bool $valid): void
    {
        $admin = User::factory()->administrator()->create();
        $member = $editing ? User::factory()->create(['position' => 'Software']) : null;
        $previousHash = $member?->password;
        $details = $this->userDetails(['password' => $password, 'password_confirmation' => $password]);
        $this->actingAs($admin);
        $response = $editing ? $this->patch(route('admin.users.update', $member), $details)
            : $this->post(route('admin.users.store'), $details);

        if ($valid) {
            $response->assertRedirect(route('admin.users.index'))->assertSessionHasNoErrors();
            $savedUser = $member?->fresh() ?? User::where('email', 'member@example.test')->sole();
            $this->assertTrue(Hash::check($password, $savedUser->password));
        } else {
            $response->assertSessionHasErrorsIn('userDetails', ['password']);
            if ($member) {
                $this->assertSame($previousHash, $member->fresh()->password);
            } else {
                $this->assertDatabaseCount('users', 1);
            }
        }
    }

    public function test_invalid_passwords_render_inline_errors_without_native_validation_popups(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.users.store'), $this->userDetails(['password' => 'Abcd123', 'password_confirmation' => 'Abcd123']))
            ->assertSessionHasErrorsIn('userDetails', ['password']);
        $this->withCookie(config('session.cookie'), $this->app['session']->getId());
        $response = $this->get(route('admin.users.create'))->assertOk();
        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $this->assertSame(1, $xpath->query('//form[@data-admin-user-form and @novalidate]')->length);
        $error = $xpath->query('//*[@data-admin-field-error="password"]')->item(0);
        $this->assertNotNull($error);
        $this->assertFalse($error->hasAttribute('hidden'));
        $this->assertStringContainsString('admin-field-error', $error->getAttribute('class'));
        $this->assertStringContainsString('8', $error->textContent);
        $this->assertSame(1, $xpath->query('//input[@name="password" and @minlength="8" and @aria-invalid="true"]')->length);
    }

    public function test_updating_details_without_a_password_keeps_the_existing_password_and_accepts_the_same_email(): void
    {
        $member = User::factory()->create(['position' => 'Programmer']);
        $originalHash = $member->password;

        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.users.update', $member), $this->userDetails([
                'email' => $member->email, 'name' => 'Updated Name', 'position' => 'Hardware',
                'password' => '', 'password_confirmation' => '',
            ]))->assertRedirect(route('admin.users.index'))->assertSessionHas('userStatus');

        $this->assertSame($originalHash, $member->fresh()->password);
        $this->assertSame('Updated Name', $member->fresh()->name);
        $this->assertSame('Hardware', $member->fresh()->position);
    }

    public function test_the_dropdown_contains_only_the_supported_positions_and_selects_the_saved_value(): void
    {
        $member = User::factory()->create(['position' => 'Hardware']);
        $response = $this->actingAs(User::factory()->administrator()->create())
            ->get(route('admin.users.edit', $member))->assertOk();
        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $positions = [];

        foreach ($xpath->query('//select[@name="position"]/option[@value!=""]') as $option) {
            $positions[] = $option->getAttribute('value');
        }

        $this->assertSame(['Software', 'Hardware'], $positions);
        $selectedOption = $xpath->query('//select[@name="position"]/option[@selected]')->item(0);
        $this->assertNotNull($selectedOption);
        $this->assertSame('Hardware', $selectedOption->getAttribute('value'));
        $this->assertSame(0, $xpath->query('//input[@name="position"]')->length);
    }

    public function test_unsupported_positions_are_rejected_on_update_without_changing_the_saved_role(): void
    {
        $member = User::factory()->create(['position' => 'Software']);

        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.users.update', $member), $this->userDetails(['position' => 'Editing']))
            ->assertSessionHasErrorsIn('userDetails', ['position']);

        $this->assertSame('Software', $member->fresh()->position);
    }

    public function test_an_admin_can_change_a_users_email_and_reset_their_password(): void
    {
        $member = User::factory()->create();
        $previousToken = $member->remember_token;

        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.users.update', $member), $this->userDetails(['email' => 'new-address@example.test']))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame('new-address@example.test', $member->fresh()->email);
        $this->assertNull($member->fresh()->email_verified_at);
        $this->assertTrue(Hash::check('MemberPassword!2026', $member->fresh()->password));
        $this->assertNotSame($previousToken, $member->fresh()->remember_token);
    }

    public function test_an_update_cannot_take_another_users_email(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.users.update', $member), $this->userDetails(['email' => $other->email]))
            ->assertSessionHasErrorsIn('userDetails', ['email']);

        $this->assertSame($member->email, $member->fresh()->email);
    }

    public function test_admin_accounts_cannot_be_edited_through_team_management(): void
    {
        $admin = User::factory()->administrator()->create();
        $otherAdmin = User::factory()->administrator()->create();
        $previousHash = $otherAdmin->password;

        $this->actingAs($admin)->get(route('admin.users.edit', $otherAdmin))->assertForbidden();
        $this->patch(route('admin.users.update', $otherAdmin), $this->userDetails())->assertForbidden();
        $this->assertSame($previousHash, $otherAdmin->fresh()->password);
        $this->assertTrue($otherAdmin->fresh()->is_admin);
    }

    public function test_user_creation_and_updates_cannot_grant_admin_access(): void
    {
        $member = User::factory()->create();
        $this->actingAs(User::factory()->administrator()->create())
            ->post(route('admin.users.store'), $this->userDetails(['is_admin' => true]))
            ->assertSessionHasErrorsIn('userDetails', ['is_admin']);
        $this->patch(route('admin.users.update', $member), $this->userDetails(['is_admin' => true]))
            ->assertSessionHasErrorsIn('userDetails', ['is_admin']);

        $this->assertDatabaseCount('users', 2);
        $this->assertFalse($member->fresh()->is_admin);
    }

    public function test_user_list_is_paginated_and_ordered_by_name(): void
    {
        $admin = User::factory()->administrator()->create();
        for ($index = 1; $index <= 16; $index++) {
            User::factory()->create(['name' => 'Team Member '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)]);
        }

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()
            ->assertSee('Team Member 01')->assertDontSee('Team Member 16')->assertSee('Next');
        $this->get(route('admin.users.index', ['page' => 2]))->assertOk()
            ->assertSee('Team Member 16')->assertDontSee('Team Member 01')->assertSee('Previous');
    }
}
