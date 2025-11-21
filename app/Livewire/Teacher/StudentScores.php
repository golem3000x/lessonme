<?php

declare(strict_types=1);

namespace App\Livewire\Teacher;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonScore;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StudentScores extends Component
{
    use WithPagination;

    public int $lesson_id;

    public function mount(int $lesson_id): void
    {
        $this->lesson_id = $lesson_id;
    }

    public function render(): Factory|View
    {
        $lesson = Lesson::with('user')->findOrFail($this->lesson_id);

        // Check authorization
        $user = Auth::user();
        if ($lesson->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $scores = LessonScore::query()
            ->where('lesson_id', '=', $this->lesson_id)
            ->with(['user', 'attempt'])
            ->orderBy('graded_at', 'desc')
            ->paginate(20);

        return view('livewire.teacher.student-scores', [
            'lesson' => $lesson,
            'scores' => $scores,
        ]);
    }
}
