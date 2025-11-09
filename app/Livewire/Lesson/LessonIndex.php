<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Models\Lesson;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonIndex extends Component
{
    public $lessons = [];

    public bool $show_manage_modal = false;

    public function mount(): void
    {
        $this->loadLessons();
    }

    #[On('lesson-created')]
    public function loadLessons(): void
    {
        $this->lessons = Lesson::where('user_id', Auth::id())
            ->with(['topicRelation' => function ($query) {
                $query->with('translations');
            }])
            ->latest()
            ->get();
    }

    public function openManageModal(): void
    {
        $this->show_manage_modal = true;
        // Dispatch event to reset manage lesson component state
        $this->dispatch('reset-manage-lesson-state');
    }

    public function closeManageModal(): void
    {
        $this->show_manage_modal = false;
    }

    public function deleteLesson(int $lesson_id): void
    {
        $lesson = Lesson::where('id', $lesson_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($lesson) {
            $lesson->delete();
            $this->loadLessons();
            session()->flash('message', __('lessons.lesson_deleted'));
        }
    }

    public function render(): Factory|View
    {
        return view('livewire.lesson.lesson-index');
    }
}
