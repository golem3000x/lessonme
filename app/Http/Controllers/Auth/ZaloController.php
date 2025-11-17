<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\SystemLanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\RegistrationNotification;
use App\Notifications\Auth\WelcomeNotification;
use App\Services\GreetingService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ZaloController extends Controller
{
    public function __construct(
        protected GreetingService $greeting_service
    ) {}

    public function redirect(): Redirector|RedirectResponse
    {
        $zalo_app_id = config('services.zalo.app_id');
        $redirect_uri = urlencode(config('services.zalo.redirect_uri'));

        return redirect("https://oauth.zaloapp.com/v4/permission?app_id=$zalo_app_id&redirect_uri=$redirect_uri&state=login");
    }

    /**
     * @throws ConnectionException
     */
    public function callback(): Redirector|RedirectResponse
    {
        $code = request('code');
        $app_id = config('services.zalo.app_id');
        $app_secret = config('services.zalo.app_secret');

        $token_response = Http::asForm()->post('https://oauth.zaloapp.com/v4/access_token', [
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        $token_data = $token_response->json();
        $access_token = $token_data['access_token'] ?? null;

        if (! $access_token) {
            return redirect('/login')->with('error', 'Zalo login failed.');
        }

        $profile = Http::withToken($access_token)
            ->get('https://graph.zalo.me/v2.0/me?fields=id,name,picture')
            ->json();

        $zalo_id = $profile['id'] ?? null;
        $name = $profile['name'] ?? 'Zalo User';
        $avatar = $profile['picture']['data']['url'] ?? null;

        if (! $zalo_id) {
            return redirect('/login')->with('error', 'Zalo user not found.');
        }

        $user = User::where('zalo_user_id', $zalo_id)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'avatar' => $avatar,
            ]);
            
            Auth::login($user);
            
            // Отправляем приветственное уведомление для существующего пользователя
            $message = $this->greeting_service->getWelcomeMessage(
                $user->name,
                $user->timezone ?? 'UTC',
                $user->native_lang ?? SystemLanguageEnum::VIETNAMESE->value
            );
            $user->notify(new WelcomeNotification($user, $message));
            
            return redirect()->route('dashboard');
        } else {
            // Новый пользователь - сохраняем данные в сессию и редиректим на выбор языка
            session()->put('registration_data', [
                'zalo_user_id' => $zalo_id,
                'name' => $name,
                'email' => 'zalo_'.$zalo_id.'@placeholder.local',
                'avatar' => $avatar,
                'password' => Hash::make(str()->random(12)),
                'email_verified_at' => now(),
                'timezone' => 'UTC',
            ]);
            
            return redirect()->route('auth.language');
        }
    }
}
