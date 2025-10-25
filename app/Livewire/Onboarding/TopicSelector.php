<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Topic\Topic;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Livewire\Component;

class TopicSelector extends Component
{
    public $selected = [];

    public function mount(): void
    {
        $this->selected = [];
    }

    public function save(): Redirector
    {
        session()->flash('message', 'Topics saved: '.implode(', ', $this->selected));

        return redirect()->route('dashboard');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        $topics = Topic::all();

        return view('livewire.onboarding.topic-selector', compact('topics'));
    }
}
