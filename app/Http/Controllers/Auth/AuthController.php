<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\SystemLanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\RegistrationNotification;
use App\Notifications\Auth\WelcomeNotification;
use App\Services\GreetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Factory;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected GreetingService $greeting_service
    ) {}

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

            // Отправляем приветственное уведомление
            $message = $this->greeting_service->getWelcomeMessage(
                $user->name,
                $timezone,
                $user->native_lang ?? SystemLanguageEnum::ENGLISH->value
            );

            $user->notify(new WelcomeNotification($user, $message));

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['Неверный email или пароль.'],
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'timezone' => $timezone,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Отправляем приветственное уведомление для нового пользователя
        $message = $this->greeting_service->getRegistrationMessage(
            $user->name,
            $timezone,
            app()->getLocale()
        );

        $user->notify(new RegistrationNotification($user, $message));

        return redirect('/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
