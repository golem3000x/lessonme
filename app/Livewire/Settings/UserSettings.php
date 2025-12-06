<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSettings extends Component
{
    public bool $show_greeting = true;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->show_greeting = $user->getSetting('show_greeting', true);
    }

    public function updatedShowGreeting(bool $value): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->setSetting('show_greeting', $value);
    }

    public function render(): Factory|View
    {
        return view('livewire.settings.user-settings');
    }
}
