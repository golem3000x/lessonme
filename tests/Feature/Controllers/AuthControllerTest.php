<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    // ===== LOGIN VALIDATION TESTS =====

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ===== REGISTER VALIDATION TESTS =====

    public function test_register_validates_required_fields(): void
    {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'password',
            'date_of_birth',
        ]);
    }

    public function test_register_validates_email_unique(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '1990-01-01',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_validates_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'date_of_birth' => '1990-01-01',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validates_password_min_length(): void
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'date_of_birth' => '1990-01-01',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validates_date_of_birth_before_today(): void
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('date_of_birth');
    }
}
