<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use Livewire\Component;

class LessonSpeaker extends Component
{
    public ?string $audio_url = null;

    public function mount(?string $audio_url = null): void
    {
        $this->audio_url = $audio_url;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.lesson.lesson-speaker');
    }
}
