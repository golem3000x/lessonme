<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonFeedback;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LessonFeedbackForm extends Component
{
    public int $lesson_id = 0;

    public bool $show_modal = false;

    public ?string $rating = null;

    public string $comment = '';

    public bool $already_submitted = false;

    public function mount(int $lesson_id): void
    {
        $this->lesson_id = $lesson_id;

        // Check if user already submitted feedback
        if (Auth::check()) {
            $existing_feedback = LessonFeedback::where('lesson_id', '=', $this->lesson_id)
                ->where('user_id', '=', Auth::id())
                ->first();

            if ($existing_feedback) {
                $this->already_submitted = true;
            }
        }
    }

    public function openModal(): void
    {
        if ($this->already_submitted) {
            return;
        }

        $this->show_modal = true;
        $this->rating = null;
        $this->comment = '';
    }

    public function closeModal(): void
    {
        $this->show_modal = false;
        $this->rating = null;
        $this->comment = '';
    }

    public function setRating(string $rating): void
    {
        $this->rating = $rating;
    }

    public function submitFeedback(): void
    {
        if (! Auth::check()) {
            return;
        }

        if ($this->already_submitted) {
            return;
        }

        $this->validate([
            'rating' => 'required|in:like,dislike',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check lesson exists
        $lesson = Lesson::find($this->lesson_id);
        if (! $lesson) {
            return;
        }

        // Create feedback
        LessonFeedback::create([
            'lesson_id' => $this->lesson_id,
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => trim($this->comment) ?: null,
        ]);

        $this->already_submitted = true;
        $this->show_modal = false;

        // Dispatch success event
        $this->dispatch('feedback-submitted');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.lesson.lesson-feedback-form');
    }
}
