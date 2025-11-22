<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\System\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_email = 'admin@example.com';

        // Check if admin already exists
        if (User::where('email', '=', $admin_email)->exists()) {
            $this->command->warn('⚠️  Admin user already exists!');

            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => $admin_email,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'native_lang' => 'vi',
            'learning_lang' => 'en',
            'role' => UserRoleEnum::ADMIN->value,
        ]);

        $this->command->info('✅ Admin user created successfully!');
        $this->command->line("   Email: {$admin_email}");
        $this->command->line('   Password: password');
        $this->command->newLine();
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
