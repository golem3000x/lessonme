<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\System\SystemKeywordsSeeder;
use Database\Seeders\System\SystemTopicsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SystemTopicsSeeder::class,
            SystemKeywordsSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
