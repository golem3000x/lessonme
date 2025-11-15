<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Models\Lesson\Lesson;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonIndex extends Component
{
    /** @var Collection<int, Lesson> */
    public Collection $lessons;

    public bool $show_manage_modal = false;

    /** @var array<int, bool> */
    public array $open_dropdowns = [];

    public function __construct()
    {
        $this->lessons = new Collection;
    }

    public function mount(): void
    {
        $this->loadLessons();
    }

    #[On('lesson-created')]
    #[On('lesson-notification')]
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

    public function toggleDropdown(int $lesson_id): void
    {
        if (isset($this->open_dropdowns[$lesson_id]) && $this->open_dropdowns[$lesson_id]) {
            $this->open_dropdowns[$lesson_id] = false;
        } else {
            // Закрываем все остальные dropdown'ы
            $this->open_dropdowns = [];
            $this->open_dropdowns[$lesson_id] = true;
        }
    }

    public function closeDropdown(int $lesson_id): void
    {
        $this->open_dropdowns[$lesson_id] = false;
    }

    public function regenerateLesson(int $lesson_id): void
    {
        // TODO: Implement lesson regeneration logic
        $this->closeDropdown($lesson_id);
        session()->flash('message', 'Regeneration feature coming soon!');
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
