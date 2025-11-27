<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\System\SystemLanguageEnum;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LanguageSelection extends Component
{
    public string $native_lang = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        // Check if registration data exists in session
        if (! session()->has('registration_data')) {
            redirect()->route('register');

            return;
        }

        $this->languages = SystemLanguageEnum::all();

        // Set default language based on locale
        $locale = app()->getLocale();
        $this->native_lang = $locale === 'vi'
            ? SystemLanguageEnum::VIETNAMESE->value
            : SystemLanguageEnum::ENGLISH->value;
    }

    public function completeRegistration(): void
    {
        $this->validate([
            'native_lang' => 'required|string|in:'.implode(',', SystemLanguageEnum::values()),
        ]);

        $registration_data = session()->get('registration_data');

        if (! $registration_data) {
            redirect()->route('register');

            return;
        }

        // Determine learning_lang (opposite language from native_lang)
        $learning_lang = $this->native_lang === SystemLanguageEnum::ENGLISH->value
            ? SystemLanguageEnum::VIETNAMESE->value
            : SystemLanguageEnum::ENGLISH->value;

        // Create user with selected language
        $user = User::create([
            'name' => $registration_data['name'],
            'email' => $registration_data['email'],
            'password' => $registration_data['password'], // Password already hashed in session
            'timezone' => $registration_data['timezone'] ?? 'UTC',
            'native_lang' => $this->native_lang,
            'learning_lang' => $learning_lang,
            'facebook_id' => $registration_data['facebook_id'] ?? null,
            'zalo_user_id' => $registration_data['zalo_user_id'] ?? null,
            'avatar' => $registration_data['avatar'] ?? null,
            'email_verified_at' => $registration_data['email_verified_at'] ?? null,
        ]);

        // Login user
        Auth::login($user);
        session()->regenerate();

        // Set a persistent cookie to indicate user has an account
        // This prevents creating temp guest users after logout
        cookie()->queue('has_account', '1', 525600); // 1 year

        // Clear registration data from session
        session()->forget('registration_data');

        // Redirect to dashboard
        redirect()->route('dashboard');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.auth.language-selection');
    }
}
