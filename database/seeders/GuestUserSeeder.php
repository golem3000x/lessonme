<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SystemLanguageEnum;
use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestUserSeeder extends Seeder
{
    public const string GUEST_EMAIL = 'guest@system.local';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if guest user already exists
        if (User::where('email', self::GUEST_EMAIL)->exists()) {
            $this->command->warn('⚠️  Guest user already exists!');

            return;
        }

        User::create([
            'name' => 'Guest User',
            'email' => self::GUEST_EMAIL,
            'password' => Hash::make(bin2hex(random_bytes(32))), // Random password
            'email_verified_at' => now(),
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
            'learning_lang' => SystemLanguageEnum::ENGLISH->value,
            'role' => UserRoleEnum::STUDENT->value,
        ]);

        $this->command->info('✅ Guest user created successfully!');
        $this->command->line('   Email: '.self::GUEST_EMAIL);
    }
}
