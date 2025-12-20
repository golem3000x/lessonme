<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\System\UserRoleEnum;
use App\Models\User\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            UserRoleEnum::ADMIN,
            UserRoleEnum::TEACHER,
            UserRoleEnum::STUDENT,
        ];

        foreach ($roles as $role_enum) {
            Role::firstOrCreate([
                'name' => $role_enum->value,
            ]);
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}
