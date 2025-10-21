<?php
declare(strict_types=1);

namespace App\Livewire\Onboarding;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileSetup extends Component
{
    public $name;
    public $native_lang = 'vi';
    public $learning_lang = 'en';
    public $preferred_time = '19:00';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->native_lang = $user->native_lang ?? 'vi';
        $this->learning_lang = $user->learning_lang ?? 'en';
    }

    public function save(): Redirector
    {
        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'native_lang' => $this->native_lang,
            'learning_lang' => $this->learning_lang,
        ]);

        session()->flash('message', 'Profile saved.');
        return redirect()->route('onboarding.topics');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
