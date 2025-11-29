<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Lesson\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanupTempGuestUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_succeeds_when_no_expired_users(): void
    {
        $this->artisan('guests:cleanup')
            ->expectsOutput('✅ No expired temporary guest users found.')
            ->assertSuccessful();
    }

    public function test_finds_expired_temp_guest_users(): void
    {
        User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('Found 1 expired temporary guest user(s).')
            ->assertSuccessful();
    }

    public function test_does_not_find_non_expired_users(): void
    {
        User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->addHour(),
        ]);

        $this->artisan('guests:cleanup')
            ->expectsOutput('✅ No expired temporary guest users found.')
            ->assertSuccessful();
    }

    public function test_does_not_find_non_temp_guest_users(): void
    {
        User::factory()->create([
            'is_temp_guest' => false,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup')
            ->expectsOutput('✅ No expired temporary guest users found.')
            ->assertSuccessful();
    }

    public function test_deletes_expired_users_with_force_flag(): void
    {
        $user = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('✅ Cleanup completed!')
            ->expectsOutput('   Deleted users: 1')
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_deletes_multiple_expired_users(): void
    {
        User::factory()->count(3)->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('Found 3 expired temporary guest user(s).')
            ->expectsOutput('   Deleted users: 3')
            ->assertSuccessful();

        $this->assertEquals(0, User::where('is_temp_guest', '=', true)->count());
    }

    public function test_deletes_users_lessons(): void
    {
        $user = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        Lesson::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('   Deleted lessons: 2')
            ->assertSuccessful();

        $this->assertEquals(0, Lesson::where('user_id', '=', $user->id)->count());
    }

    public function test_requires_confirmation_without_force_flag(): void
    {
        User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup')
            ->expectsQuestion('Do you want to delete these users and their lessons?', false)
            ->expectsOutput('Cleanup cancelled.')
            ->assertSuccessful();

        $this->assertEquals(1, User::where('is_temp_guest', '=', true)->count());
    }

    public function test_proceeds_with_user_confirmation(): void
    {
        $user = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup')
            ->expectsQuestion('Do you want to delete these users and their lessons?', true)
            ->expectsOutput('✅ Cleanup completed!')
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_displays_starting_message(): void
    {
        $this->artisan('guests:cleanup')
            ->expectsOutput('🧹 Starting cleanup of expired temporary guest users...')
            ->assertSuccessful();
    }

    public function test_displays_cleanup_completed_message(): void
    {
        $user = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
            'email' => 'guest@example.com',
        ]);

        Lesson::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('✅ Cleanup completed!')
            ->assertSuccessful();
    }

    public function test_keeps_non_expired_temp_guests(): void
    {
        $expired = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $active = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->addHour(),
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $expired->id]);
        $this->assertDatabaseHas('users', ['id' => $active->id]);
    }

    public function test_keeps_regular_users(): void
    {
        User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $regular_user = User::factory()->create([
            'is_temp_guest' => false,
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $regular_user->id]);
    }

    public function test_handles_users_without_lessons(): void
    {
        User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('   Deleted lessons: 0')
            ->assertSuccessful();
    }

    public function test_counts_total_deleted_lessons_across_users(): void
    {
        $user1 = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        $user2 = User::factory()->create([
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->subHour(),
        ]);

        Lesson::factory()->count(3)->create(['user_id' => $user1->id]);
        Lesson::factory()->count(2)->create(['user_id' => $user2->id]);

        $this->artisan('guests:cleanup', ['--force' => true])
            ->expectsOutput('   Deleted users: 2')
            ->expectsOutput('   Deleted lessons: 5')
            ->assertSuccessful();
    }
}
