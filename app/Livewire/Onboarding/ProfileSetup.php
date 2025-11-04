<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\SystemLanguageEnum;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileSetup extends Component
{
    public string $name = '';

    public string $native_lang;

    public string $learning_lang;

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->native_lang = $user->native_lang;
        $this->learning_lang = $user->learning_lang;

        $this->languages = SystemLanguageEnum::all();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'native_lang' => 'required|string|different:learning_lang',
            'learning_lang' => 'required|string|different:native_lang',
        ], [
            'native_lang.different' => __('onboarding.languages_must_be_different'),
            'learning_lang.different' => __('onboarding.languages_must_be_different'),
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'name' => $this->name,
            'native_lang' => $this->native_lang,
            'learning_lang' => $this->learning_lang,
        ]);

        session()->flash('message', __('onboarding.profile_saved'));
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
