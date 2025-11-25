<?php

declare(strict_types=1);

namespace App\Livewire\Help;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickTourBanner extends Component
{
    public bool $show_banner = true;

    public function mount(): void
    {
        // Show banner only if user hasn't completed first login
        $user = Auth::user();
        if ($user && $user->first_login_completed_at !== null) {
            $this->show_banner = false;
        }
    }

    /**
     * Start the interactive tour
     */
    public function startTour(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'first_login_completed_at' => now(),
            ]);
        }

        $this->show_banner = false;

        // Dispatch event to frontend to start Shepherd tour
        $this->dispatch('start-shepherd-tour');
    }

    /**
     * Dismiss banner temporarily (will show again on next visit)
     */
    public function dismissTemporarily(): void
    {
        $this->show_banner = false;
        // Don't save to database - will show on next dashboard visit
    }

    /**
     * Dismiss banner permanently
     */
    public function dismissPermanently(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'first_login_completed_at' => now(),
            ]);
        }

        $this->show_banner = false;
    }

    /**
     * Handle tour completion event from frontend
     */
    #[On('tour-completed')]
    public function handleTourCompleted(): void
    {
        $user = Auth::user();
        if ($user && $user->first_login_completed_at === null) {
            $user->update([
                'first_login_completed_at' => now(),
            ]);
        }

        $this->show_banner = false;
    }

    public function render(): View
    {
        return view('livewire.help.quick-tour-banner');
    }
}
