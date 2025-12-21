<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Factory;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Validate timezone against PHP's timezone list.
     */
    private function validateTimezone(?string $timezone, string $default = 'UTC'): string
    {
        if ($timezone === null || $timezone === '') {
            return $default;
        }

        $valid_timezones = \DateTimeZone::listIdentifiers();

        return in_array($timezone, $valid_timezones, true) ? $timezone : $default;
    }

    public function showLogin(): Factory|View
    {
        return view('auth.login');
    }

    public function showRegister(): Factory|View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => [__('auth.failed')],
                ]);
            }

            // Get and validate timezone from request (if sent from browser)
            $timezone = $this->validateTimezone(
                $request->input('timezone'),
                $user->timezone ?? 'UTC'
            );

            // Update user timezone if changed
            if ($user->timezone !== $timezone) {
                $user->update(['timezone' => $timezone]);
            }

            // Set a persistent cookie to indicate user has an account
            // This prevents creating temp guest users after logout
            cookie()->queue('has_account', '1', 525600); // 1 year

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
        ]);

        // Get and validate timezone from request (if sent from browser)
        $timezone = $this->validateTimezone($request->input('timezone'), 'UTC');

        // Save registration data to session (with hashed password)
        $request->session()->put('registration_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'date_of_birth' => $request->date_of_birth,
            'timezone' => $timezone,
        ]);

        // Redirect to native language selection
        return redirect()->route('auth.language');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
