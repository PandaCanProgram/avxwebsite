<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_login_page_has_a_csrf_protected_password_manager_friendly_form(): void
    {
        $response = $this->get(route('admin.login'))->assertOk();

        $response->assertSee('name="_token"', false)
            ->assertSee('autocomplete="username"', false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee('type="password"', false)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertDontSee('googletagmanager.com');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_an_administrator_can_sign_in_and_the_session_id_is_rotated(): void
    {
        $admin = User::factory()->administrator()->create(['password' => 'AdminTestPassword!2026']);
        $this->withSession(['marker' => 'retained']);
        $previousSessionId = $this->app['session']->getId();

        $this->post(route('admin.login.store'), [
            'email' => ' '.strtoupper($admin->email).' ',
            'password' => 'AdminTestPassword!2026',
        ])->assertRedirect(route('admin.dashboard'))->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($previousSessionId, $this->app['session']->getId());
        $this->get(route('admin.dashboard'))->assertOk()->assertSee($admin->email);
    }

    public function test_incorrect_credentials_do_not_sign_in_or_flash_the_password(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'IncorrectPassword!2026',
        ])->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['email' => 'The email or password is incorrect.'])
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest();
    }

    public function test_unknown_accounts_receive_the_same_error(): void
    {
        $this->postJson(route('admin.login.store'), [
            'email' => 'unknown@example.test',
            'password' => 'IncorrectPassword!2026',
        ])->assertUnprocessable()->assertJsonValidationErrors([
            'email' => 'The email or password is incorrect.',
        ]);

        $this->assertGuest();
    }

    public function test_ordinary_users_cannot_sign_in_to_the_admin_area(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_ordinary_authenticated_users_cannot_bypass_the_admin_check(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_login_requires_an_email_and_password(): void
    {
        $this->postJson(route('admin.login.store'), [])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_repeated_failed_logins_are_blocked_until_the_lockout_expires(): void
    {
        $admin = User::factory()->administrator()->create(['password' => 'AdminTestPassword!2026']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('admin.login.store'), [
                'email' => $admin->email, 'password' => 'incorrect',
            ])->assertUnprocessable();
        }

        $response = $this->postJson(route('admin.login.store'), [
            'email' => $admin->email, 'password' => 'AdminTestPassword!2026',
        ])->assertUnprocessable();
        $this->assertStringContainsString('Too many sign-in attempts.', $response->json('errors.email.0'));
        $this->assertGuest();

        $this->travel(61)->seconds();
        $this->post(route('admin.login.store'), [
            'email' => $admin->email, 'password' => 'AdminTestPassword!2026',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_ip_rate_limit_also_blocks_attempts_that_rotate_email_addresses(): void
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->postJson(route('admin.login.store'), [
                'email' => 'attempt'.$attempt.'@example.test', 'password' => 'incorrect',
            ])->assertUnprocessable();
        }

        $this->postJson(route('admin.login.store'), [
            'email' => 'another@example.test', 'password' => 'incorrect',
        ])->assertTooManyRequests();
    }

    public function test_dashboard_is_private_and_signed_in_admins_skip_the_login_page(): void
    {
        $admin = User::factory()->administrator()->create();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee($admin->email);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->get(route('admin.login'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_logout_clears_the_session_and_blocks_dashboard_access(): void
    {
        $this->actingAs(User::factory()->administrator()->create())->withSession(['private-marker' => 'value']);
        $previousSessionId = $this->app['session']->getId();
        $previousToken = $this->app['session']->token();

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'))
            ->assertSessionMissing('private-marker');

        $this->assertGuest();
        $this->assertNotSame($previousSessionId, $this->app['session']->getId());
        $this->assertNotSame($previousToken, $this->app['session']->token());
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_logout_cannot_be_triggered_by_a_get_request(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->get('/admin/logout')->assertStatus(405);
        $this->assertAuthenticatedAs($admin);
    }
}
