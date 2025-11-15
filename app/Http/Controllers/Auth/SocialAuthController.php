<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
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

            $user = User::where('facebook_id', $facebook_user->getId())->first();

            if (!$user) {
                // Check if user with this email already exists
                $user = User::where('email', $facebook_user->getEmail())->first();

                if ($user) {
                    // Update existing user with Facebook ID
                    $user->update([
                        'facebook_id' => $facebook_user->getId(),
                        'avatar' => $facebook_user->getAvatar(),
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $facebook_user->getName(),
                        'email' => $facebook_user->getEmail(),
                        'facebook_id' => $facebook_user->getId(),
                        'avatar' => $facebook_user->getAvatar(),
                        'password' => Hash::make(Str::random(24)),
                        'email_verified_at' => now(),
                    ]);
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

            return redirect()->intended('/');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Facebook. Please try again.');
        }
    }
}
