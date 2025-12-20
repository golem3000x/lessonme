<?php

declare(strict_types=1);

namespace App\Livewire\Course;

use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\Course\CourseProgress;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseShow extends Component
{
    public Course $course;

    /** @var array<int,array{id:int,uuid:string,name:string,order:int,is_locked:bool,is_passed:bool,score:int|null}> */
    public array $lessons_with_progress = [];

    public int $completed_count = 0;

    public int $total_count = 0;

    public int $progress_percentage = 0;

    public function mount(Course $course): void
    {
        $user = Auth::user();

        // Check if user has access to this course
        $is_owner = $course->user_id === $user->id;
        $is_assigned = CourseAssignment::query()
            ->where('course_id', '=', $course->id)
            ->where('student_id', '=', $user->id)
            ->exists();

        if (! $is_owner && ! $is_assigned) {
            throw new NotFoundHttpException;
        }

        $this->course = $course;
        $this->loadProgress();
    }

    protected function loadProgress(): void
    {
        $user = Auth::user();
        $lessons = $this->course->lessons()->orderByPivot('order')->get();

        $this->total_count = $lessons->count();
        $this->completed_count = 0;
        $this->lessons_with_progress = [];

        $previous_passed = true;

        foreach ($lessons as $lesson) {
            $progress = CourseProgress::where('course_id', $this->course->id)
                ->where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            $is_passed = $progress?->is_passed ?? false;
            $is_locked = ! $previous_passed;

            if ($is_passed) {
                $this->completed_count++;
            }

            $this->lessons_with_progress[] = [
                'id' => $lesson->id,
                'uuid' => $lesson->uuid,
                'name' => $lesson->lesson_name,
                'order' => $lesson->pivot->order,
                'is_locked' => $is_locked,
                'is_passed' => $is_passed,
                'score' => $progress?->score,
            ];

            $previous_passed = $is_passed;
        }

        $this->progress_percentage = $this->total_count > 0
            ? (int) round(($this->completed_count / $this->total_count) * 100)
            : 0;
    }

    public function render(): View
    {
        return view('livewire.course.course-show');
    }
}
