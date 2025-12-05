<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\System\SystemLanguageEnum;
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
        $user = User::where('email', '=', $admin_email)->first();

        if ($user) {
            // Ensure admin role is assigned
            $user->assignRole(UserRoleEnum::ADMIN);
            $this->command->warn('⚠️  Admin user already exists! Role verified.');

            return;
        }

        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => $admin_email,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'date_of_birth' => '1990-01-01',
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
            'learning_lang' => SystemLanguageEnum::ENGLISH->value,
        ]);

        // Assign admin role
        $user->assignRole(UserRoleEnum::ADMIN);

        $this->command->info('✅ Admin user created successfully!');
        $this->command->line("   Email: {$admin_email}");
        $this->command->line('   Password: password');
        $this->command->newLine();
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
