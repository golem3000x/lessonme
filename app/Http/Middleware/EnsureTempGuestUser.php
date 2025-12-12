<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\System\SystemLanguageEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsureTempGuestUser
{
    /**
     * Handle an incoming request.
     * Creates a temporary guest user if not authenticated and no temp guest exists in session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is already authenticated
        if (auth()->check()) {
            return $next($request);
        }

        // Skip if user has a real account (was previously authenticated)
        // Check for 'has_account' cookie that persists after logout
        if ($request->cookie('has_account')) {
            return $next($request);
        }

        // Check if we already have a temp guest user in session
        $temp_guest_user_id = $request->session()->get('temp_guest_user_id');

        if ($temp_guest_user_id) {
            // Verify the user still exists and is valid
            $temp_guest_exists = User::where('id', '=', $temp_guest_user_id)
                ->where('is_temp_guest', '=', true)
                ->where('temp_guest_expires_at', '>', now())
                ->exists();

            if ($temp_guest_exists) {
                return $next($request);
            }
        }

        // Create a new temporary guest user only for genuinely new visitors
        $unique_id = uniqid('guest_', true);
        $temp_guest_user = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Guest',
            'date_of_birth' => now()->subYears(25)->toDateString(),
            'email' => 'demo_'.$unique_id.'@temp.local',
            'password' => Hash::make(bin2hex(random_bytes(32))),
            'email_verified_at' => now(),
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
            'is_temp_guest' => true,
            'temp_guest_expires_at' => now()->addHours(24),
        ]);

        // Add learning languages for temp guest
        $temp_guest_user->syncLearningLanguages([SystemLanguageEnum::ENGLISH->value]);

        // Store in session
        $request->session()->put('temp_guest_user_id', $temp_guest_user->id);
        $request->session()->put('temp_guest_user_uuid', $temp_guest_user->uuid);
        $request->session()->save();

        return $next($request);
    }
}
