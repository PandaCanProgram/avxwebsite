<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_setup_command_creates_an_administrator_with_a_hashed_password(): void
    {
        $this->artisan('admin:create', ['--email' => ' Admin@Example.test '])
            ->expectsQuestion('Password', 'AdminTestPassword!2026')->assertExitCode(0);

        $admin = User::sole();
        $this->assertSame('admin@example.test', $admin->email);
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('AdminTestPassword!2026', $admin->password));
        $this->assertNotSame('AdminTestPassword!2026', $admin->password);
    }

    public function test_the_setup_command_does_not_overwrite_or_promote_an_existing_user(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.test']);
        $originalPassword = $user->password;

        $this->artisan('admin:create', ['--email' => $user->email])->assertExitCode(1);

        $this->assertDatabaseCount('users', 1);
        $this->assertFalse($user->fresh()->is_admin);
        $this->assertSame($originalPassword, $user->fresh()->password);
    }

    public function test_the_setup_command_rejects_short_passwords(): void
    {
        $this->artisan('admin:create')->expectsQuestion('Password', 'short')->assertExitCode(1);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_noninteractive_setup_without_a_password_fails_without_creating_a_user(): void
    {
        $this->artisan('admin:create', ['--no-interaction' => true])->assertExitCode(1);
        $this->assertDatabaseCount('users', 0);
    }
}
