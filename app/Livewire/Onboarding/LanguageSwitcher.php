<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $current_locale = '';

    public string $target_locale = '';

    public function mount(): void
    {
        $this->current_locale = app()->getLocale();
        $this->target_locale = $this->current_locale === 'vi' ? 'en' : 'vi';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.language-switcher');
    }
}
