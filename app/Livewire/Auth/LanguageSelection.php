<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\System\SystemLanguageEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    public function proceedToRoleSelection(): void
    {
        $this->validate([
            'native_lang' => 'required|string|in:'.implode(',', SystemLanguageEnum::values()),
        ]);

        $registration_data = session()->get('registration_data');

        if (! $registration_data) {
            redirect()->route('register');

            return;
        }

        // Save native_lang to session for next step
        session()->put('registration_native_lang', $this->native_lang);

        // Redirect to role selection
        redirect()->route('auth.role');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.auth.language-selection');
    }
}
