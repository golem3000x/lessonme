<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create 
                            {--email= : Admin email}
                            {--name= : Admin name}
                            {--password= : Admin password}
                            {--native-lang=vi : Native language (default: vi)}
                            {--learning-lang=en : Learning language (default: en)}';

    protected $description = 'Create a new admin user';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Admin email');
        $name = $this->option('name') ?? $this->ask('Admin name');
        $password = $this->option('password') ?? $this->secret('Admin password');
        $native_lang = $this->option('native-lang');
        $learning_lang = $this->option('learning-lang');

        // Validation
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // Create admin
        $user = User::create([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
            'native_lang' => $native_lang,
            'learning_lang' => $learning_lang,
        ]);

        $this->newLine();
        $this->info('✅ Admin user created successfully!');
        $this->newLine();
        $this->table(
            ['ID', 'Name', 'Email', 'Is Admin', 'Native Lang', 'Learning Lang'],
            [[
                $user->id,
                $user->name,
                $user->email,
                $user->is_admin ? 'Yes' : 'No',
                $user->native_lang,
                $user->learning_lang,
            ]]
        );

        return self::SUCCESS;
    }
}
