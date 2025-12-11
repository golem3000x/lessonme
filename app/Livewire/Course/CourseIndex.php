<?php

declare(strict_types=1);

namespace App\Livewire\Course;

use App\Models\Course\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class CourseIndex extends Component
{
    public bool $show_delete_confirm = false;

    public ?int $course_to_delete = null;

    public function confirmDelete(int $course_id): void
    {
        $this->course_to_delete = $course_id;
        $this->show_delete_confirm = true;
    }

    public function cancelDelete(): void
    {
        $this->show_delete_confirm = false;
        $this->course_to_delete = null;
    }

    public function deleteCourse(): void
    {
        if ($this->course_to_delete === null) {
            return;
        }

        $user = Auth::user();
        $course = Course::where('id', $this->course_to_delete)
            ->where('user_id', $user->id)
            ->first();

        if ($course) {
            $course->delete();
            session()->flash('message', __('lessons.course_deleted'));
        }

        $this->cancelDelete();
    }

    public function togglePublish(int $course_id): void
    {
        $user = Auth::user();
        $course = Course::where('id', $course_id)
            ->where('user_id', $user->id)
            ->first();

        if ($course) {
            $course->update(['is_published' => ! $course->is_published]);
            session()->flash('message', $course->is_published
                ? __('lessons.course_published')
                : __('lessons.course_unpublished')
            );
        }
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        $user = Auth::user();

        return Course::where('user_id', $user->id)
            ->withCount('lessons')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.course.course-index', [
            'courses' => $this->getCourses(),
        ]);
    }
}
