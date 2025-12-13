<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\UserRoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_creates_admin_with_options(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
            '--native-lang' => SystemLanguageEnum::VIETNAMESE->value,
            '--learning-lang' => SystemLanguageEnum::ENGLISH->value,
        ])
            ->expectsOutput('✅ Admin user created successfully!')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);

        $user = User::where('email', '=', 'admin@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_creates_admin_with_default_languages(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);
    }

    public function test_validates_required_email(): void
    {
        $this->artisan('admin:create', [
            '--email' => '',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_email_format(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'invalid-email',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_unique_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $this->artisan('admin:create', [
            '--email' => 'existing@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(1, User::count());
    }

    public function test_validates_required_first_name(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => '',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_required_last_name(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => '',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_first_name_max_length(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => str_repeat('a', 256),
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_required_password(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => '',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_validates_password_min_length(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'short',
        ])
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_hashes_password(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertSuccessful();

        $user = User::where('email', '=', 'admin@example.com')->first();
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Hash::check('password123', $user->password));
    }

    public function test_sets_email_verified_at(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertSuccessful();

        $user = User::where('email', '=', 'admin@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_sets_admin_role(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->assertSuccessful();

        $user = User::where('email', '=', 'admin@example.com')->first();
        $this->assertTrue($user->hasRole(UserRoleEnum::ADMIN));
    }

    public function test_displays_success_message(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
        ])
            ->expectsOutput('✅ Admin user created successfully!')
            ->assertSuccessful();
    }

    public function test_can_create_multiple_admins(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin1@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'One',
            '--password' => 'password123',
        ])->assertSuccessful();

        $this->artisan('admin:create', [
            '--email' => 'admin2@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'Two',
            '--password' => 'password123',
        ])->assertSuccessful();

        $this->assertEquals(2, User::whereHas('roles', fn ($q) => $q->where('name', UserRoleEnum::ADMIN->value))->count());
    }

    public function test_supports_custom_languages(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'password123',
            '--native-lang' => SystemLanguageEnum::ENGLISH->value,
            '--learning-lang' => SystemLanguageEnum::VIETNAMESE->value,
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
        ]);
    }
}
