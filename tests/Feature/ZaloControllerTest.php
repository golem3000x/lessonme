<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZaloControllerTest extends TestCase
{
    public function test_redirect_builds_zalo_oauth_url_from_config(): void
    {
        config()->set('services.zalo.app_id', '123456');
        config()->set('services.zalo.redirect_uri', 'http://localhost/auth/zalo/callback');

        $response = $this->get(route('zalo.login'));

        $expected = 'https://oauth.zaloapp.com/v4/permission?app_id=123456&redirect_uri='.urlencode('http://localhost/auth/zalo/callback').'&state=login';
        $response->assertRedirect($expected);
    }

    public function test_callback_redirects_back_to_login_when_token_exchange_fails(): void
    {
        config()->set('services.zalo.app_id', 'app');
        config()->set('services.zalo.app_secret', 'secret');

        Http::fake([
            'oauth.zaloapp.com/v4/access_token' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $response = $this->get('/auth/zalo/callback?code=abc');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Zalo login failed.');
    }

    public function test_callback_redirects_back_to_login_when_profile_has_no_id(): void
    {
        config()->set('services.zalo.app_id', 'app');
        config()->set('services.zalo.app_secret', 'secret');

        Http::fake([
            'oauth.zaloapp.com/v4/access_token' => Http::response(['access_token' => 'token-123'], 200),
            'graph.zalo.me/*' => Http::response(['name' => 'No ID User', 'picture' => ['data' => ['url' => 'http://img.example/avatar.png']]], 200),
        ]);

        $response = $this->get('/auth/zalo/callback?code=abc');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Zalo user not found.');
    }

    public function test_callback_creates_user_logs_in_and_redirects_to_dashboard(): void
    {
        config()->set('services.zalo.app_id', 'app');
        config()->set('services.zalo.app_secret', 'secret');

        Http::fake([
            'oauth.zaloapp.com/v4/access_token' => Http::response(['access_token' => 'token-123'], 200),
            'graph.zalo.me/*' => Http::response([
                'id' => 'zalo-uid-1',
                'name' => 'Zed Lao',
                'picture' => ['data' => ['url' => 'http://img.example/pic.jpg']],
            ], 200),
        ]);

        $response = $this->get('/auth/zalo/callback?code=abc');

        // New users are redirected to language selection
        $response->assertRedirect(route('auth.language'));

        // User is not created yet - it's saved in session
        $this->assertNull(User::where('zalo_user_id', '=', 'zalo-uid-1')->first());

        // Check session has registration data
        $this->assertTrue(session()->has('registration_data'));
        $registration_data = session('registration_data');
        $this->assertSame('zalo-uid-1', $registration_data['zalo_user_id']);
        $this->assertSame('Zed Lao', $registration_data['name']);
        $this->assertSame('http://img.example/pic.jpg', $registration_data['avatar']);
    }

    public function test_callback_updates_existing_user_when_zalo_id_already_exists(): void
    {
        config()->set('services.zalo.app_id', 'app');
        config()->set('services.zalo.app_secret', 'secret');

        $existing = User::factory()->create([
            'zalo_user_id' => 'zalo-uid-2',
            'name' => 'Old Name',
            'avatar' => 'http://old.example/old.jpg',
        ]);

        Http::fake([
            'oauth.zaloapp.com/v4/access_token' => Http::response(['access_token' => 'token-123'], 200),
            'graph.zalo.me/*' => Http::response([
                'id' => 'zalo-uid-2',
                'name' => 'New Name',
                'picture' => ['data' => ['url' => 'http://img.example/new.jpg']],
            ], 200),
        ]);

        $response = $this->get('/auth/zalo/callback?code=abc');

        $response->assertRedirect(route('dashboard'));

        $existing->refresh();
        $this->assertSame('New Name', $existing->name);
        $this->assertSame('http://img.example/new.jpg', $existing->avatar);
        $this->assertAuthenticatedAs($existing);
    }
}
