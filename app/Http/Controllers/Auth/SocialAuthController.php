<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\SystemLanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\WelcomeNotification;
use App\Services\GreetingService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(
        protected GreetingService $greeting_service
    ) {}

    /**
     * Redirect to Facebook for authentication
     */
    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback(): RedirectResponse
    {
        try {
            $facebook_user = Socialite::driver('facebook')->user();

            $user = User::where('facebook_id', '=', $facebook_user->getId())->first();
            $is_new_user = false;

            if (! $user) {
                // Check if user with this email already exists
                $user = User::where('email', '=', $facebook_user->getEmail())->first();

                if ($user) {
                    // Update existing user with Facebook ID
                    $user->update([
                        'facebook_id' => $facebook_user->getId(),
                        'avatar' => $facebook_user->getAvatar(),
                    ]);
                } else {
                    // Новый пользователь - сохраняем данные в сессию и редиректим на выбор языка
                    session()->put('registration_data', [
                        'name' => $facebook_user->getName(),
                        'email' => $facebook_user->getEmail(),
                        'facebook_id' => $facebook_user->getId(),
                        'avatar' => $facebook_user->getAvatar(),
                        'password' => Hash::make(Str::random(24)),
                        'email_verified_at' => now(),
                        'timezone' => 'UTC',
                    ]);

                    return redirect()->route('auth.language');
                }
            } else {
                // Update avatar if changed
                if ($user->avatar !== $facebook_user->getAvatar()) {
                    $user->update([
                        'avatar' => $facebook_user->getAvatar(),
                    ]);
                }
            }

            Auth::login($user, true);

            // Отправляем приветственное уведомление для существующего пользователя
            $message = $this->greeting_service->getWelcomeMessage(
                $user->name,
                $user->timezone ?? 'UTC',
                $user->native_lang ?? SystemLanguageEnum::ENGLISH->value
            );
            $user->notify(new WelcomeNotification($user, $message));

            return redirect()->intended('/');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Facebook. Please try again.');
        }
    }
}
