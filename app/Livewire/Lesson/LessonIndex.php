<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\QueueNameEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Course\Course;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\User\User;
use App\Notifications\Lesson\LessonAssignedNotification;
use App\Notifications\Lesson\LessonAssignmentRemovedNotification;
use App\Notifications\Lesson\LessonAssignmentSentNotification;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Notifications\Lesson\LessonUnassignedNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class LessonIndex extends Component
{
    use WithPagination;

    public bool $show_manage_modal = false;

    public int $manage_modal_key = 0;

    /** @var array<int, bool> */
    public array $open_dropdowns = [];

    public bool $show_delete_confirm = false;

    public ?int $lesson_to_delete = null;

    public bool $show_regenerate_confirm = false;

    public ?int $lesson_to_regenerate = null;

    public bool $show_assign_modal = false;

    public ?int $lesson_to_assign = null;

    /** @var array<int, int> */
    public array $selected_students = [];

    public bool $show_course_modal = false;

    public ?int $lesson_for_course = null;

    public ?int $selected_course_id = null;

    /** @var array<int, array{id: int, title: string, lessons_count: int}> */
    public array $available_courses = [];

    // Filters UI state
    public bool $show_filters = false;

    public bool $show_level_dropdown = false;

    public bool $show_language_dropdown = false;

    public bool $show_sort_dropdown = false;

    // Filter values (temporary, not applied yet)
    public string $temp_search = '';

    /** @var array<int, string> */
    public array $temp_language_levels = [];

    /** @var array<int, string> */
    public array $temp_lesson_languages = [];

    // Applied filter values
    public string $search = '';

    /** @var array<int, string> */
    public array $language_levels = [];

    /** @var array<int, string> */
    public array $lesson_languages = [];

    public string $sort = 'newest';

    // View mode: 'my_lessons' or 'assigned'
    public string $view_mode = 'my_lessons';

    public function mount(): void
    {
        // Restore filters from session
        $this->search = session('lesson_filters.search', '');
        $this->language_levels = session('lesson_filters.language_levels', []);
        $this->lesson_languages = session('lesson_filters.lesson_languages', []);
        $this->sort = session('lesson_filters.sort', 'newest');

        // Initialize temp filters with applied values
        $this->temp_search = $this->search;
        $this->temp_language_levels = $this->language_levels;
        $this->temp_lesson_languages = $this->lesson_languages;
    }

    public function updated(string $property): void
    {
        // Save sort to session when it changes
        if ($property === 'sort') {
            session(['lesson_filters.sort' => $this->sort]);
            $this->resetPage();
        }
    }

    public function setSort(string $sort_value): void
    {
        $this->sort = $sort_value;
        session(['lesson_filters.sort' => $this->sort]);
        $this->resetPage();
    }

    /**
     * Handle lesson creation/regeneration notification from WebSocket
     * Refreshes the lesson list
     */
    #[On('lesson-updated')]
    public function lessonUpdated(): void
    {
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Lesson>
     */
    protected function getLessonsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->view_mode === 'assigned') {
            // Get lessons assigned to the current user (student view)
            $assigned_lesson_ids = LessonAssignment::query()
                ->where('student_id', '=', $user->id)
                ->pluck('lesson_id')
                ->toArray();

            $query = Lesson::whereIn('id', $assigned_lesson_ids)
                ->with(['topic.translations']);
        } else {
            // Get user's own lessons
            $query = Lesson::where('user_id', '=', $user->id)
                ->with(['topic.translations']);
        }

        // Apply search filter
        if ($this->search) {
            $query->where('lesson_name', 'like', '%'.$this->search.'%');
        }

        // Apply language level filter
        if (! empty($this->language_levels)) {
            $query->whereIn('language_level', $this->language_levels);
        }

        // Apply lesson language filter
        if (! empty($this->lesson_languages)) {
            $query->whereIn('lesson_lang', $this->lesson_languages);
        }

        // Apply sorting
        if ($this->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        return $query;
    }

    public function setViewMode(string $mode): void
    {
        $this->view_mode = $mode;
        $this->resetPage();
    }

    public function openManageModal(): void
    {
        $this->manage_modal_key++;
        $this->show_manage_modal = true;
        // Dispatch event to reset manage lesson component state
        $this->dispatch('reset-manage-lesson-state');
    }

    #[On('close-manage-modal')]
    public function closeManageModal(): void
    {
        $this->js('document.body.style.overflow = "auto"');
        $this->show_manage_modal = false;
    }

    public function toggleDropdown(int $lesson_id): void
    {
        if (isset($this->open_dropdowns[$lesson_id]) && $this->open_dropdowns[$lesson_id]) {
            $this->open_dropdowns[$lesson_id] = false;
        } else {
            // Close all other dropdowns
            $this->open_dropdowns = [];
            $this->open_dropdowns[$lesson_id] = true;
        }
    }

    public function closeDropdown(int $lesson_id): void
    {
        $this->open_dropdowns[$lesson_id] = false;
    }

    public function confirmRegenerate(int $lesson_id): void
    {
        $this->lesson_to_regenerate = $lesson_id;
        $this->show_regenerate_confirm = true;
        $this->closeDropdown($lesson_id);
    }

    public function cancelRegenerate(): void
    {
        $this->show_regenerate_confirm = false;
        $this->lesson_to_regenerate = null;
    }

    public function regenerateLesson(): void
    {
        if ($this->lesson_to_regenerate === null) {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $lesson = Lesson::where('id', '=', $this->lesson_to_regenerate)
            ->where('user_id', '=', $user->id)
            ->with(['topic.translations', 'topic.keywords.translations'])
            ->first();

        if (! $lesson) {
            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::ERROR,
                null,
            ));

            $this->cancelRegenerate();

            return;
        }

        try {
            $locale = app()->getLocale();
            $topic_name = $lesson->topic?->title($locale) ?? '';

            // Get keywords from topic
            $keywords = [];
            if ($lesson->topic && $lesson->topic->relationLoaded('keywords')) {
                $keywords = $lesson->topic->keywords
                    ->map(fn ($keyword) => $keyword->value($locale))
                    ->filter()
                    ->values()
                    ->all();
            }

            // If no keywords, use topic name
            if (empty($keywords)) {
                $keywords = [$topic_name];
            }

            $lesson_dto = new LessonDto(
                $topic_name,
                $lesson->language_level,
                $user->native_lang,
                $lesson->lesson_lang->value,
                $keywords
            );

            // Set lesson status to REGENERATION
            $lesson->update(['status' => LessonCreationStatusEnum::REGENERATION]);

            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::REGENERATION,
                $lesson->id,
            ));

            // Dispatch job for async lesson regeneration
            RegenerateLessonJob::dispatch(
                $this->lesson_to_regenerate,
                $lesson_dto
            )
                ->onQueue(QueueNameEnum::LESSON->value);

        } catch (\Exception $e) {
            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::ERROR,
                null,
            ));
        }

        $this->cancelRegenerate();
    }

    public function confirmDelete(int $lesson_id): void
    {
        $this->lesson_to_delete = $lesson_id;
        $this->show_delete_confirm = true;
        $this->closeDropdown($lesson_id);
    }

    public function cancelDelete(): void
    {
        $this->show_delete_confirm = false;
        $this->lesson_to_delete = null;
    }

    public function deleteLesson(): void
    {
        if ($this->lesson_to_delete === null) {
            return;
        }

        $lesson = Lesson::where('id', '=', $this->lesson_to_delete)
            ->where('user_id', Auth::id())
            ->first();

        if ($lesson) {
            $lesson->delete();
            $this->resetPage();
            session()->flash('message', __('lessons.lesson_deleted'));
        }

        $this->cancelDelete();
    }

    public function openAssignModal(int $lesson_id): void
    {
        $this->lesson_to_assign = $lesson_id;
        $this->selected_students = [];
        $this->show_assign_modal = true;
        $this->closeDropdown($lesson_id);
    }

    public function closeAssignModal(): void
    {
        $this->js('document.body.style.overflow = "auto"');
        $this->show_assign_modal = false;
        $this->lesson_to_assign = null;
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

    public function assignLesson(): void
    {
        if ($this->lesson_to_assign === null || empty($this->selected_students)) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        if (! $teacher->isTeacher()) {
            return;
        }

        $lesson = Lesson::find($this->lesson_to_assign);

        if (! $lesson) {
            return;
        }

        $assigned_count = 0;

        foreach ($this->selected_students as $student_id) {
            // Check if already assigned
            $exists = LessonAssignment::query()
                ->where('lesson_id', '=', $lesson->id)
                ->where('student_id', '=', $student_id)
                ->exists();

            if ($exists) {
                continue;
            }

            // Create assignment
            $assignment = LessonAssignment::create([
                'lesson_id' => $lesson->id,
                'teacher_id' => $teacher->id,
                'student_id' => $student_id,
                'assigned_at' => now(),
                'status' => AssignmentStatusEnum::PENDING->value,
            ]);

            // Send notification to student
            $student = User::find($student_id);

            if ($student instanceof User) {
                $student->notify(new LessonAssignedNotification($assignment));
            }

            $assigned_count++;
        }

        if ($assigned_count > 0) {
            // Send notification to teacher (current user)
            $teacher->notify(new LessonAssignmentSentNotification($teacher, $lesson, $assigned_count));
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
     * Get IDs of students already assigned to the current lesson.
     *
     * @return array<int>
     */
    #[Computed]
    public function assignedStudentIds(): array
    {
        if ($this->lesson_to_assign === null) {
            return [];
        }

        return LessonAssignment::query()
            ->where('lesson_id', '=', $this->lesson_to_assign)
            ->pluck('student_id')
            ->toArray();
    }

    public function assignSingleStudent(int $student_id): void
    {
        if ($this->lesson_to_assign === null) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        if (! $teacher->isTeacher()) {
            return;
        }

        $lesson = Lesson::find($this->lesson_to_assign);
        $student = User::find($student_id);

        if (! $lesson || ! $student) {
            return;
        }

        // Check if already assigned
        $exists = LessonAssignment::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('student_id', '=', $student_id)
            ->exists();

        if ($exists) {
            return;
        }

        // Create assignment
        $assignment = LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student_id,
            'assigned_at' => now(),
            'status' => AssignmentStatusEnum::PENDING->value,
        ]);

        // Notify student
        $student->notify(new LessonAssignedNotification($assignment));

        // Notify teacher (confirmation)
        $teacher->notify(new LessonAssignmentSentNotification($teacher, $lesson, 1));

        // Clear computed cache
        unset($this->assignedStudentIds);
    }

    public function unassignStudent(int $student_id): void
    {
        if ($this->lesson_to_assign === null) {
            return;
        }

        /** @var User $teacher */
        $teacher = Auth::user();

        $lesson = Lesson::find($this->lesson_to_assign);
        $student = User::find($student_id);

        if (! $lesson || ! $student) {
            return;
        }

        LessonAssignment::query()
            ->where('lesson_id', '=', $this->lesson_to_assign)
            ->where('student_id', '=', $student_id)
            ->delete();

        // Notify student that they were unassigned
        $student->notify(new LessonUnassignedNotification($student, $lesson, $teacher->name));

        // Notify teacher (confirmation)
        $teacher->notify(new LessonAssignmentRemovedNotification($teacher, $lesson, $student->name));

        // Clear computed cache
        unset($this->assignedStudentIds);
    }

    // Course modal methods
    public function openAddToCourseModal(int $lesson_id): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->isTeacher()) {
            return;
        }

        $this->lesson_for_course = $lesson_id;
        $this->selected_course_id = null;

        // Load user's courses
        $this->available_courses = Course::query()
            ->where('user_id', $user->id)
            ->withCount('lessons')
            ->get()
            ->map(fn (Course $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'lessons_count' => $c->lessons_count,
            ])
            ->toArray();

        $this->show_course_modal = true;
        $this->closeDropdown($lesson_id);
    }

    public function closeCourseModal(): void
    {
        $this->show_course_modal = false;
        $this->lesson_for_course = null;
        $this->selected_course_id = null;
        $this->available_courses = [];
    }

    public function addLessonToCourse(): void
    {
        if ($this->lesson_for_course === null || $this->selected_course_id === null) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $course_id = (int) $this->selected_course_id;

        $course = Course::where('id', $course_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $course) {
            return;
        }

        // Check if already in course
        if ($course->lessons()->where('lessons.id', $this->lesson_for_course)->exists()) {
            session()->flash('error', __('lessons.lesson_already_in_course'));
            $this->closeCourseModal();

            return;
        }

        // Get max order
        $max_order = $course->lessons()->max('order') ?? 0;

        // Attach lesson to course
        $course->lessons()->attach($this->lesson_for_course, [
            'order' => $max_order + 1,
            'is_required' => true,
        ]);

        session()->flash('message', __('lessons.lesson_added_to_course'));
        $this->closeCourseModal();
    }

    public function toggleFilters(): void
    {
        $this->show_filters = ! $this->show_filters;

        // Sync temp filters with current applied filters when opening
        if ($this->show_filters) {
            $this->temp_search = $this->search;
            $this->temp_language_levels = $this->language_levels;
            $this->temp_lesson_languages = $this->lesson_languages;
        }

        // Close dropdowns when closing filters panel
        if (! $this->show_filters) {
            $this->show_level_dropdown = false;
            $this->show_language_dropdown = false;
        }
    }

    public function applyFilters(): void
    {
        $this->search = $this->temp_search;
        $this->language_levels = $this->temp_language_levels;
        $this->lesson_languages = $this->temp_lesson_languages;

        // Save filters to session
        session([
            'lesson_filters.search' => $this->search,
            'lesson_filters.language_levels' => $this->language_levels,
            'lesson_filters.lesson_languages' => $this->lesson_languages,
        ]);

        $this->resetPage();
        $this->show_filters = false;
        $this->show_level_dropdown = false;
        $this->show_language_dropdown = false;
    }

    public function resetFilters(): void
    {
        $this->temp_search = '';
        $this->temp_language_levels = [];
        $this->temp_lesson_languages = [];

        $this->search = '';
        $this->language_levels = [];
        $this->lesson_languages = [];

        // Clear filters from session (keep sort)
        session()->forget(['lesson_filters.search', 'lesson_filters.language_levels', 'lesson_filters.lesson_languages']);

        $this->resetPage();
        $this->show_level_dropdown = false;
        $this->show_language_dropdown = false;
    }

    #[Computed]
    public function filteredLessonsCount(): int
    {
        $query = Lesson::where('user_id', '=', Auth::id());

        // Apply temporary search filter
        if ($this->temp_search) {
            $query->where('lesson_name', 'like', '%'.$this->temp_search.'%');
        }

        // Apply temporary language level filter
        if (! empty($this->temp_language_levels)) {
            $query->whereIn('language_level', $this->temp_language_levels);
        }

        // Apply temporary lesson language filter
        if (! empty($this->temp_lesson_languages)) {
            $query->whereIn('lesson_lang', $this->temp_lesson_languages);
        }

        return $query->count();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || ! empty($this->language_levels) || ! empty($this->lesson_languages) || $this->sort !== 'newest';
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function getLanguageLevels(): array
    {
        $levels = [];

        foreach (LanguageLevelEnum::cases() as $level) {
            $levels[] = [
                'value' => $level->value,
                'label' => $level->value,
            ];
        }

        return $levels;
    }

    /**
     * @return array<int, array{value: string, label: string, flag: string}>
     */
    public function getLessonLanguages(): array
    {
        $languages = [];

        foreach (SystemLanguageEnum::cases() as $language) {
            $languages[] = [
                'value' => $language->value,
                'label' => $language->label(),
                'flag' => $language->flag(),
            ];
        }

        return $languages;
    }

    public function render(): Factory|View
    {
        $lessons = $this->getLessonsQuery()->paginate(12);

        return view('livewire.lesson.lesson-index', [
            'lessons' => $lessons,
        ]);
    }
}
