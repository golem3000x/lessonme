<?php

declare(strict_types=1);

namespace App\Livewire\Course;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\UserRoleEnum;
use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\User;
use App\Notifications\Course\CourseAssignedNotification;
use App\Notifications\Course\CourseAssignmentRemovedNotification;
use App\Notifications\Course\CourseAssignmentSentNotification;
use App\Notifications\Course\CourseUnassignedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class CourseIndex extends Component
{
    public bool $show_delete_confirm = false;

    public ?int $course_to_delete = null;

    // View mode: 'my_courses' or 'assigned'
    public string $view_mode = 'my_courses';

    // Assignment modal
    public bool $show_assign_modal = false;

    public ?int $course_to_assign = null;

    /** @var array<int> */
    public array $selected_students = [];

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

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        $user = Auth::user();

        if ($this->view_mode === 'my_courses' && $this->isTeacher()) {
            // My Courses: Teacher sees their own courses
            return Course::where('user_id', $user->id)
                ->with('user')
                ->withCount('lessons')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Assigned Courses: Courses assigned to the current user
        $assigned_course_ids = CourseAssignment::query()
            ->where('student_id', '=', $user->id)
            ->pluck('course_id')
            ->toArray();

        return Course::whereIn('id', $assigned_course_ids)
            ->with('user')
            ->withCount('lessons')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function openAssignModal(int $course_id): void
    {
        $this->course_to_assign = $course_id;
        $this->selected_students = [];
        $this->show_assign_modal = true;
    }

    public function closeAssignModal(): void
    {
        $this->js('document.body.style.overflow = "auto"');
        $this->show_assign_modal = false;
        $this->course_to_assign = null;
        $this->selected_students = [];
    }

    public function toggleStudentSelection(int $student_id): void
    {
        if (in_array($student_id, $this->selected_students)) {
            $this->selected_students = array_values(array_diff($this->selected_students, [$student_id]));
        } else {
            $this->selected_students[] = $student_id;
        }
    }

    public function assignCourse(): void
    {
        if ($this->course_to_assign === null || empty($this->selected_students)) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        if (! $teacher->isTeacher()) {
            return;
        }

        $course = Course::find($this->course_to_assign);

        if (! $course) {
            return;
        }

        $assigned_count = 0;

        foreach ($this->selected_students as $student_id) {
            // Check if already assigned
            $exists = CourseAssignment::query()
                ->where('course_id', '=', $course->id)
                ->where('student_id', '=', $student_id)
                ->exists();

            if ($exists) {
                continue;
            }

            // Create assignment
            $assignment = CourseAssignment::create([
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'student_id' => $student_id,
                'assigned_at' => now(),
                'status' => AssignmentStatusEnum::PENDING->value,
            ]);

            // Send notification to student
            /** @var User $student */
            $student = User::find($student_id);

            if ($student) {
                $student->notify(new CourseAssignedNotification($assignment));
            }

            $assigned_count++;
        }

        if ($assigned_count > 0) {
            // Send notification to teacher (current user)
            $teacher->notify(new CourseAssignmentSentNotification($teacher, $course, $assigned_count));
        }

        $this->closeAssignModal();
    }

    /**
     * Get list of teacher's students.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function myStudents(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->students()->get();
    }

    /**
     * Get IDs of students already assigned to the current course.
     *
     * @return array<int>
     */
    #[Computed]
    public function assignedStudentIds(): array
    {
        if ($this->course_to_assign === null) {
            return [];
        }

        return CourseAssignment::query()
            ->where('course_id', '=', $this->course_to_assign)
            ->pluck('student_id')
            ->toArray();
    }

    public function assignSingleStudent(int $student_id): void
    {
        if ($this->course_to_assign === null) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        if (! $teacher->isTeacher()) {
            return;
        }

        $course = Course::find($this->course_to_assign);
        $student = User::find($student_id);

        if (! $course || ! $student) {
            return;
        }

        // Check if already assigned
        $exists = CourseAssignment::query()
            ->where('course_id', '=', $course->id)
            ->where('student_id', '=', $student_id)
            ->exists();

        if ($exists) {
            return;
        }

        // Create assignment
        $assignment = CourseAssignment::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student_id,
            'assigned_at' => now(),
            'status' => AssignmentStatusEnum::PENDING->value,
        ]);

        // Notify student
        $student->notify(new CourseAssignedNotification($assignment));

        // Notify teacher (confirmation)
        $teacher->notify(new CourseAssignmentSentNotification($teacher, $course, 1));

        // Clear computed cache
        unset($this->assignedStudentIds);
    }

    public function unassignStudent(int $student_id): void
    {
        if ($this->course_to_assign === null) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        $course = Course::find($this->course_to_assign);
        $student = User::find($student_id);

        if (! $course || ! $student) {
            return;
        }

        CourseAssignment::query()
            ->where('course_id', '=', $this->course_to_assign)
            ->where('student_id', '=', $student_id)
            ->delete();

        // Notify student that they were unassigned
        $student->notify(new CourseUnassignedNotification($student, $course, $teacher->full_name));

        // Notify teacher (confirmation)
        $teacher->notify(new CourseAssignmentRemovedNotification($teacher, $course, $student->full_name));

        // Clear computed cache
        unset($this->assignedStudentIds);
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
