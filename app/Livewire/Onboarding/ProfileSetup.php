<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\AvailableLanguageEnum;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileSetup extends Component
{
    public string $name = '';

    public int $native_lang_id;

    public int $learning_lang_id;

    public string $preferred_time = '19:00';

    /** @var \App\Enums\AvailableLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->native_lang_id = $user->native_lang_id ?? AvailableLanguageEnum::Vietnamese->value;
        $this->learning_lang_id = $user->learning_lang_id ?? AvailableLanguageEnum::English->value;

        $this->languages = AvailableLanguageEnum::all();
    }

    public function save(): Redirector
    {
        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'native_lang_id' => $this->native_lang_id,
            'learning_lang_id' => $this->learning_lang_id,
        ]);

        session()->flash('message', __('onboarding.profile_saved'));

        return redirect()->route('onboarding.topics');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
