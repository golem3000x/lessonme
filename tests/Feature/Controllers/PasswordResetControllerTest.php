<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_forgot_password_form_displays_view(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_send_reset_link_validates_required_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_send_reset_link_validates_email_format(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_send_reset_link_sends_email_to_valid_user(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    public function test_send_reset_link_fails_for_non_existent_user(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_show_reset_password_form_displays_view_with_token(): void
    {
        $token = 'test-token';

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
    }

    public function test_reset_validates_required_token(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => '',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['token']);
    }

    public function test_reset_validates_required_email(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => 'test-token',
            'email' => '',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_validates_required_password(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => 'test-token',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_validates_password_confirmation(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => 'test-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_updates_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_fails_for_non_existent_email(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => 'test-token',
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_generates_new_remember_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'remember_token' => 'old-token',
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $user->refresh();
        $this->assertNotEquals('old-token', $user->remember_token);
    }
}
