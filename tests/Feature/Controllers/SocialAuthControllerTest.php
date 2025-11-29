<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_redirect_to_facebook_redirects_to_socialite(): void
    {
        $response = $this->get('/auth/facebook');

        $response->assertRedirect();
    }

    public function test_facebook_callback_creates_new_user_with_language_selection(): void
    {
        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect(route('auth.language'));
        $this->assertTrue(session()->has('registration_data'));
        $this->assertEquals('Test User', session('registration_data.name'));
        $this->assertEquals('newuser@example.com', session('registration_data.email'));
        $this->assertEquals('facebook-id-123', session('registration_data.facebook_id'));
        $this->assertEquals('https://example.com/avatar.jpg', session('registration_data.avatar'));
    }

    public function test_facebook_callback_logs_in_existing_user_with_facebook_id(): void
    {
        $user = User::factory()->create([
            'facebook_id' => 'facebook-id-123',
            'email' => 'existing@example.com',
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_facebook_callback_updates_existing_user_avatar(): void
    {
        $user = User::factory()->create([
            'facebook_id' => 'facebook-id-123',
            'email' => 'existing@example.com',
            'avatar' => 'old-avatar.jpg',
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/new-avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $this->get('/auth/facebook/callback');

        $user->refresh();
        $this->assertEquals('https://example.com/new-avatar.jpg', $user->avatar);
    }

    public function test_facebook_callback_links_facebook_id_to_existing_email(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'facebook_id' => null,
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertEquals('facebook-id-123', $user->facebook_id);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar);
    }

    public function test_facebook_callback_sets_has_account_cookie_on_login(): void
    {
        $user = User::factory()->create([
            'facebook_id' => 'facebook-id-123',
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $response = $this->get('/auth/facebook/callback');

        $response->assertCookie('has_account', '1');
    }

    public function test_facebook_callback_handles_exception_gracefully(): void
    {
        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andThrow(new \Exception('OAuth failed'));

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_facebook_callback_stores_email_verified_at_for_new_user(): void
    {
        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $this->get('/auth/facebook/callback');

        $this->assertNotNull(session('registration_data.email_verified_at'));
    }

    public function test_facebook_callback_stores_random_password_for_new_user(): void
    {
        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $this->get('/auth/facebook/callback');

        $password = session('registration_data.password');
        $this->assertNotEmpty($password);
        $this->assertTrue(Hash::check('', $password) === false); // Not empty password
    }

    public function test_facebook_callback_does_not_update_avatar_if_unchanged(): void
    {
        $avatar = 'https://example.com/avatar.jpg';
        $user = User::factory()->create([
            'facebook_id' => 'facebook-id-123',
            'avatar' => $avatar,
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn($avatar);

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $this->get('/auth/facebook/callback');

        $user->refresh();
        $this->assertEquals($avatar, $user->avatar);
    }

    public function test_facebook_callback_logs_in_with_remember_me(): void
    {
        $user = User::factory()->create([
            'facebook_id' => 'facebook-id-123',
        ]);

        $facebook_user = Mockery::mock(SocialiteUser::class);
        $facebook_user->shouldReceive('getId')->andReturn('facebook-id-123');
        $facebook_user->shouldReceive('getEmail')->andReturn('existing@example.com');
        $facebook_user->shouldReceive('getName')->andReturn('Test User');
        $facebook_user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($facebook_user);

        $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
    }
}
