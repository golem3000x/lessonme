<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\SystemLanguageEnum;
use App\Models\User;
use App\Notifications\Auth\RegistrationNotification;
use App\Services\GreetingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LanguageSelection extends Component
{
    public string $native_lang = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        // Проверяем, есть ли данные регистрации в сессии
        if (!session()->has('registration_data')) {
            redirect()->route('register');
            return;
        }

        $this->languages = SystemLanguageEnum::all();
        
        // Устанавливаем язык по умолчанию на основе локали
        $locale = app()->getLocale();
        $this->native_lang = $locale === 'vi' 
            ? SystemLanguageEnum::VIETNAMESE->value 
            : SystemLanguageEnum::ENGLISH->value;
    }

    public function completeRegistration(): void
    {
        $this->validate([
            'native_lang' => 'required|string|in:' . implode(',', SystemLanguageEnum::values()),
        ]);

        $registration_data = session()->get('registration_data');
        
        if (!$registration_data) {
            redirect()->route('register');
            return;
        }

        // Определяем learning_lang (противоположный язык от native_lang)
        $learning_lang = $this->native_lang === SystemLanguageEnum::ENGLISH->value 
            ? SystemLanguageEnum::VIETNAMESE->value 
            : SystemLanguageEnum::ENGLISH->value;

        // Создаем пользователя с выбранным языком
        $user = User::create([
            'name' => $registration_data['name'],
            'email' => $registration_data['email'],
            'password' => $registration_data['password'], // Пароль уже хеширован в сессии
            'timezone' => $registration_data['timezone'] ?? 'UTC',
            'native_lang' => $this->native_lang,
            'learning_lang' => $learning_lang,
            'facebook_id' => $registration_data['facebook_id'] ?? null,
            'zalo_user_id' => $registration_data['zalo_user_id'] ?? null,
            'avatar' => $registration_data['avatar'] ?? null,
            'email_verified_at' => $registration_data['email_verified_at'] ?? null,
        ]);

        // Логиним пользователя
        Auth::login($user);
        session()->regenerate();

        // Отправляем приветственное уведомление
        $greeting_service = app(GreetingService::class);
        $message = $greeting_service->getRegistrationMessage(
            $user->name,
            $user->timezone ?? 'UTC',
            $this->native_lang
        );

        $user->notify(new RegistrationNotification($user, $message));

        // Очищаем данные регистрации из сессии
        session()->forget('registration_data');

        // Редиректим на дашборд
        redirect()->route('dashboard');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.auth.language-selection');
    }
}
