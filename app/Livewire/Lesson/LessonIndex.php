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
        $this->load_lessons();
    }

    #[On('lesson-created')]
    public function load_lessons(): void
    {
        $this->lessons = Lesson::where('user_id', Auth::id())
            ->with(['topicRelation' => function ($query) {
                $query->with('translations');
            }])
            ->latest()
            ->get();
    }

    public function open_manage_modal(): void
    {
        $this->show_manage_modal = true;
        // Dispatch event to reset manage lesson component state
        $this->dispatch('reset-manage-lesson-state');
    }

    public function close_manage_modal(): void
    {
        $this->show_manage_modal = false;
    }

    public function delete_lesson(int $lesson_id): void
    {
        $lesson = Lesson::where('id', $lesson_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($lesson) {
            $lesson->delete();
            $this->load_lessons();
            session()->flash('message', __('lessons.lesson_deleted'));
        }
    }

    public function render(): Factory|View
    {
        return view('livewire.lesson.lesson-index');
    }
}
