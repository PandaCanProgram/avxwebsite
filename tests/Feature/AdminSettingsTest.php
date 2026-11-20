<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Foundation\ArrayMaintenanceMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->instance(MaintenanceMode::class, new ArrayMaintenanceMode);
    }

    public function test_settings_show_the_sidebar_account_preferences_and_website_status(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->get(route('admin.settings'))->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertSee('Appearance')->assertSee('Change Password')->assertSee('Website Maintenance')
            ->assertSee('Website Is Live')->assertSee('data-admin-theme="light"', false);
    }

    #[TestWith(['get', 'admin.settings'])]
    #[TestWith(['patch', 'admin.settings.password'])]
    #[TestWith(['patch', 'admin.settings.appearance'])]
    #[TestWith(['patch', 'admin.settings.maintenance'])]
    public function test_only_administrators_can_access_settings(string $method, string $route): void
    {
        $this->{$method}(route($route))->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->{$method}(route($route))->assertForbidden();
        $this->assertFalse($this->app->make(MaintenanceMode::class)->active());
    }

    public function test_password_change_requires_the_correct_current_password_and_does_not_flash_secrets(): void
    {
        $admin = User::factory()->administrator()->create();
        $originalHash = $admin->password;

        $this->actingAs($admin)->patch(route('admin.settings.password'), [
            'current_password' => 'incorrect',
            'password' => 'NewAdminPassword!2026',
            'password_confirmation' => 'NewAdminPassword!2026',
        ])->assertRedirect(route('admin.settings').'#password')
            ->assertSessionHasErrorsIn('passwordUpdate', ['current_password'])
            ->assertSessionMissing('_old_input.current_password')
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');

        $this->assertSame($originalHash, $admin->fresh()->password);
    }

    #[TestWith(['short', 'short'])]
    #[TestWith(['NewAdminPassword!2026', 'DifferentConfirmation!2026'])]
    #[TestWith(['OriginalAdminPassword!2026', 'OriginalAdminPassword!2026'])]
    public function test_invalid_new_passwords_do_not_change_the_account(string $password, string $confirmation): void
    {
        $admin = User::factory()->administrator()->create(['password' => 'OriginalAdminPassword!2026']);
        $originalHash = $admin->password;

        $this->actingAs($admin)->patch(route('admin.settings.password'), [
            'current_password' => 'OriginalAdminPassword!2026',
            'password' => $password, 'password_confirmation' => $confirmation,
        ])->assertSessionHasErrorsIn('passwordUpdate', ['password']);

        $this->assertSame($originalHash, $admin->fresh()->password);
    }

    public function test_password_change_is_hashed_preserves_the_current_session_and_replaces_the_old_login(): void
    {
        $admin = User::factory()->administrator()->create();
        $previousToken = $admin->remember_token;
        $this->actingAs($admin)->get(route('admin.settings'))->assertOk();
        $previousSessionId = $this->app['session']->getId();

        $this->patch(route('admin.settings.password'), [
            'current_password' => 'password',
            'password' => 'NewAdminPassword!2026',
            'password_confirmation' => 'NewAdminPassword!2026',
        ])->assertRedirect(route('admin.settings').'#password')->assertSessionHas('passwordUpdated');

        $this->assertTrue(Hash::check('NewAdminPassword!2026', $admin->fresh()->password));
        $this->assertFalse(Hash::check('password', $admin->fresh()->password));
        $this->assertNotSame($previousToken, $admin->fresh()->remember_token);
        $this->assertNotSame($previousSessionId, $this->app['session']->getId());
        $this->get(route('admin.settings'))->assertOk();
        $this->post(route('admin.logout'));
        $this->postJson(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertUnprocessable();
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'NewAdminPassword!2026'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_a_session_with_the_previous_password_hash_is_signed_out(): void
    {
        $admin = User::factory()->administrator()->create();
        $this->actingAs($admin)->get(route('admin.settings'))->assertOk();
        $previousSessionHash = $this->app['session']->get('password_hash_web');

        $this->patch(route('admin.settings.password'), [
            'current_password' => 'password',
            'password' => 'NewAdminPassword!2026',
            'password_confirmation' => 'NewAdminPassword!2026',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin->fresh())->withSession(['password_hash_web' => $previousSessionHash])
            ->get(route('admin.settings'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    #[TestWith(['dark'])]
    #[TestWith(['light'])]
    public function test_theme_is_saved_to_the_current_admin_and_applied_on_other_admin_pages(string $theme): void
    {
        $admin = User::factory()->administrator()->create();
        $otherAdmin = User::factory()->administrator()->create();

        $this->actingAs($admin)->patch(route('admin.settings.appearance'), ['theme' => $theme])
            ->assertRedirect(route('admin.settings').'#appearance')->assertSessionHas('appearanceUpdated');

        $this->assertSame($theme, $admin->fresh()->admin_theme);
        $this->assertSame('light', $otherAdmin->fresh()->admin_theme);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('data-admin-theme="'.$theme.'"', false);
    }

    public function test_invalid_theme_does_not_replace_the_saved_preference(): void
    {
        $admin = User::factory()->administrator()->create(['admin_theme' => 'dark']);

        $this->actingAs($admin)->patch(route('admin.settings.appearance'), ['theme' => 'unsupported'])
            ->assertSessionHasErrorsIn('appearanceUpdate', ['theme']);

        $this->assertSame('dark', $admin->fresh()->admin_theme);
    }

    public function test_maintenance_can_be_enabled_and_disabled_without_losing_admin_access(): void
    {
        $admin = User::factory()->administrator()->create();
        $maintenance = $this->app->make(MaintenanceMode::class);

        $this->actingAs($admin)->patch(route('admin.settings.maintenance'), ['maintenance_enabled' => true])
            ->assertRedirect(route('admin.settings').'#maintenance')->assertSessionHas('maintenanceUpdated');
        $this->assertTrue($maintenance->active());
        $this->get('/')->assertServiceUnavailable()->assertHeader('Retry-After', '60')->assertSee('Back Soon.', false);
        $this->get(route('admin.settings'))->assertOk()->assertSee('Maintenance Mode Is On');
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.login'))->assertOk();
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))->assertOk();
        $this->patch(route('admin.settings.maintenance'), ['maintenance_enabled' => false])
            ->assertRedirect(route('admin.settings').'#maintenance');
        $this->assertFalse($maintenance->active());
        $this->get('/')->assertOk();
    }

    #[TestWith(['get', '/projects'])]
    #[TestWith(['get', '/investment'])]
    #[TestWith(['get', '/drone-platforms'])]
    #[TestWith(['get', '/sitemap.xml'])]
    #[TestWith(['get', '/robots.txt'])]
    #[TestWith(['post', '/contact'])]
    #[TestWith(['post', '/contact/check-email'])]
    #[TestWith(['post', '/investment/inquiries'])]
    #[TestWith(['post', '/alpha-registration'])]
    #[TestWith(['post', '/testimonials'])]
    public function test_public_pages_and_forms_are_blocked_during_maintenance(string $method, string $path): void
    {
        $this->app->make(MaintenanceMode::class)->activate(['status' => 503, 'retry' => 60]);
        $this->{$method}($path)->assertServiceUnavailable();
        $this->assertDatabaseCount('inquiries', 0);
        $this->assertDatabaseCount('alpha_registrations', 0);
    }

    public function test_invalid_maintenance_value_leaves_the_website_live(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.settings.maintenance'), ['maintenance_enabled' => 'invalid'])
            ->assertSessionHasErrorsIn('maintenanceUpdate', ['maintenance_enabled']);

        $this->assertFalse($this->app->make(MaintenanceMode::class)->active());
    }

    public function test_saving_an_already_active_maintenance_setting_preserves_its_payload(): void
    {
        $maintenance = $this->app->make(MaintenanceMode::class);
        $payload = ['status' => 503, 'retry' => 90, 'time' => 123456];
        $maintenance->activate($payload);

        $this->actingAs(User::factory()->administrator()->create())
            ->patch(route('admin.settings.maintenance'), ['maintenance_enabled' => true])->assertSessionHasNoErrors();

        $this->assertSame($payload, $maintenance->data());
    }
}
