<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\System\SystemLanguageEnum;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $current_locale = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        $this->current_locale = app()->getLocale();
        $this->languages = SystemLanguageEnum::all();
    }

    public function getCurrentFlag(): string
    {
        return SystemLanguageEnum::flagByValue($this->current_locale);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.onboarding.language-switcher');
    }
}
