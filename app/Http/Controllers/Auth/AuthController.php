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

            // Получаем timezone из запроса (если отправлен из браузера)
            $timezone = $request->input('timezone', $user->timezone ?? 'UTC');

            // Обновляем timezone пользователя если он изменился
            if ($user->timezone !== $timezone) {
                $user->update(['timezone' => $timezone]);
            }

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
        ]);

        // Получаем timezone из запроса (если отправлен из браузера)
        $timezone = $request->input('timezone', 'UTC');

        // Сохраняем данные регистрации в сессию (с хешированным паролем)
        $request->session()->put('registration_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'timezone' => $timezone,
        ]);

        // Редиректим на выбор родного языка
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
