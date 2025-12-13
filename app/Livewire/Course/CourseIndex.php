<?php

declare(strict_types=1);

namespace App\Livewire\Course;

use App\Enums\System\UserRoleEnum;
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

    // View mode: 'my_courses' or 'available'
    public string $view_mode = 'my_courses';

    public function setViewMode(string $mode): void
    {
        $this->view_mode = $mode;
    }

    public function isTeacher(): bool
    {
        $user = Auth::user();

        return $user && $user->hasRole(UserRoleEnum::TEACHER);
    }

    public function confirmDelete(int $course_id): void
    {
        if (! $this->isTeacher()) {
            return;
        }

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
        if (! $this->isTeacher() || $this->course_to_delete === null) {
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
        if (! $this->isTeacher()) {
            return;
        }

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

        if ($this->view_mode === 'my_courses' && $this->isTeacher()) {
            // My Courses: Teacher sees their own courses (including drafts)
            return Course::where('user_id', $user->id)
                ->with('user')
                ->withCount('lessons')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Available Courses: All published courses (for students and teachers in 'available' mode)
        return Course::where('is_published', true)
            ->with('user')
            ->withCount('lessons')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.course.course-index', [
            'courses' => $this->getCourses(),
            'is_teacher' => $this->isTeacher(),
            'view_mode' => $this->view_mode,
        ]);
    }
}
