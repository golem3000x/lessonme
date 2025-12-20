<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;

class CleanupTempGuestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:cleanup
                            {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired temporary guest users and their lessons';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Starting cleanup of expired temporary guest users...');

        // Find expired temp guest users
        $expired_users = User::where('is_temp_guest', '=', true)
            ->where('temp_guest_expires_at', '<', now())
            ->get();

        if ($expired_users->isEmpty()) {
            $this->info('✅ No expired temporary guest users found.');

            return Command::SUCCESS;
        }

        $count = $expired_users->count();
        $this->info("Found {$count} expired temporary guest user(s).");

        if (! $this->option('force') && ! $this->confirm('Do you want to delete these users and their lessons?')) {
            $this->info('Cleanup cancelled.');

            return Command::SUCCESS;
        }

        $deleted_count = 0;
        $lessons_deleted_count = 0;

        foreach ($expired_users as $user) {
            $lessons_count = $user->lessons()->count();

            // Delete will cascade to lessons due to foreign key constraint
            $user->delete();

            $deleted_count++;
            $lessons_deleted_count += $lessons_count;

            $this->line("  Deleted user #{$user->id} ({$user->email}) with {$lessons_count} lesson(s)");
        }

        $this->info('✅ Cleanup completed!');
        $this->info("   Deleted users: {$deleted_count}");
        $this->info("   Deleted lessons: {$lessons_deleted_count}");

        return Command::SUCCESS;
    }
}
