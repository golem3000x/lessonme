<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Topic\Topic;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TopicSelector extends Component
{
    /** @var array<int> */
    public array $selected = [];

    public function mount(): void
    {
        $this->selected = [];
    }

    public function save()
    {
        $selected_topics = Topic::with('translations')
            ->whereIn('id', $this->selected)
            ->get();

        $topic_labels = $selected_topics
            ->map(fn (Topic $topic): string => $topic->translatedValue())
            ->implode(', ');

        session()->flash('message', 'Topics saved: '.$topic_labels);

        return redirect()->route('dashboard');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        $topics = Topic::with('translations')->get();

        return view('livewire.onboarding.topic-selector', compact('topics'));
    }
}
