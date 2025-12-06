<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

/**
 * PLACEHOLDER SYSTEM EXPLANATION:
 *
 * This component uses TWO simple arrays stored in session to track lessons being created/regenerated:
 *
 * 1. creating_lessons_placeholders (flat array)
 *    - Stores placeholder IDs for NEW lessons being created
 *    - Not linked to any lesson ID (lessons don't exist yet)
 *    - When notification arrives, removes FIRST element (FIFO)
 *    - Example: ['creating_123', 'creating_456']
 *
 * 2. regenerating_lessons_placeholders (associative array)
 *    - Stores placeholder IDs for lessons being REGENERATED
 *    - Key: lesson database ID, Value: placeholder ID
 *    - When notification arrives with lesson_database_id, removes SPECIFIC element
 *    - Example: [42 => 'regenerating_789', 55 => 'regenerating_012']
 *
 * FLOW FOR CREATING NEW LESSON:
 * 1. User clicks create → ManageLesson.createLesson()
 * 2. Placeholder added to session + dispatch('placeholder-added')
 * 3. LessonIndex catches 'placeholder-added' → adds to local state (REAL-TIME!)
 * 4. Job dispatched → lesson created in DB
 * 5. WebSocket notification → component refreshes
 * 6. render() gets TOTAL COUNT → if increased → remove placeholder(s)
 *
 * FLOW FOR REGENERATING LESSON:
 * 1. User clicks regenerate → LessonIndex.regenerateLesson()
 * 2. Placeholder added to session with lesson_id as key
 * 3. Job dispatched → lesson updated in DB (count doesn't change!)
 * 4. WebSocket notification → component refreshes
 * 5. render() checks if lesson_id appears on current page → removes placeholder
 *
 * SIMPLE APPROACH:
 * - creating: if total_count > previous_count → remove placeholder (FIFO)
 * - regenerating: if lesson_id is on current page → remove placeholder
 *
 * FIRST RENDER LOGIC:
 * - When page loads: previous_count = 0, but lessons already exist
 * - Don't clean placeholders! Just initialize: previous_count = current_count
 * - This prevents false "new lesson" detection on page load
 *
 * EDGE CASES (count doesn't increase):
 * - EXISTS: lesson already exists → count unchanged → use force_cleanup flag
 * - ERROR: creation failed → count unchanged → use force_cleanup flag
 * echo.js sends force_cleanup=true for these cases
 *
 * WHY IT WORKS:
 * - Total count is independent of pagination/filters
 * - First render initializes baseline, subsequent renders detect changes
 * - Regenerating lessons don't change count, so we check by ID presence
 * - Force cleanup handles cases where count won't increase but placeholder must go
 */

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Enums\Notifications\LessonRegenerationStatusEnum;
use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\QueueNameEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\User;
use App\Notifications\Lesson\LessonAssignedNotification;
use App\Notifications\Lesson\LessonAssignmentRemovedNotification;
use App\Notifications\Lesson\LessonAssignmentSentNotification;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Notifications\Lesson\LessonRegenerationNotification;
use App\Notifications\Lesson\LessonUnassignedNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Placeholders for NEW lessons being created (simple flat array of placeholder IDs)
     * Each element is just a unique string ID
     *
     * @var array<int, string>
     */
    public array $creating_lessons_placeholders = [];

    /**
     * Placeholders for lessons being REGENERATED (associative array)
     * Key: lesson database ID, Value: ['id' => placeholder_id, 'timestamp' => time()]
     *
     * @var array<int, array{id: string, timestamp: int}>
     */
    public array $regenerating_lessons_placeholders = [];

    /**
     * Track total lessons count from previous render
     * MUST be public so Livewire persists it between renders
     */
    public int $previous_lessons_count = 0;

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

        // Restore placeholders from session
        $this->creating_lessons_placeholders = session('lesson_placeholders_creating', []);
        $this->regenerating_lessons_placeholders = session('lesson_placeholders_regenerating', []);
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
     * Add placeholder to local state when user creates new lesson
     * Called from ManageLesson after adding to session
     *
     * @param  string  $placeholder_id  - Unique placeholder ID
     */
    #[On('placeholder-added')]
    public function placeholderAdded(string $placeholder_id): void
    {
        // Add to local state for immediate UI update
        $this->creating_lessons_placeholders[] = $placeholder_id;
        // Session already updated by ManageLesson
    }

    /**
     * Handle lesson creation notification from WebSocket
     *
     * @param  int|null  $lesson_database_id  - Database ID if lesson exists/created, null on error
     * @param  bool  $force_cleanup  - Force cleanup even if count didn't increase (for EXISTS/ERROR cases)
     */
    #[On('lesson-created')]
    public function lessonCreated(?int $lesson_database_id = null, bool $force_cleanup = false): void
    {
        // If forced cleanup (EXISTS or ERROR) - remove placeholder immediately
        if ($force_cleanup) {
            if ($lesson_database_id !== null && isset($this->regenerating_lessons_placeholders[$lesson_database_id])) {
                // Regeneration failed/exists
                unset($this->regenerating_lessons_placeholders[$lesson_database_id]);
                session(['lesson_placeholders_regenerating' => $this->regenerating_lessons_placeholders]);
            } else {
                // Creation failed/exists - remove first placeholder
                if (! empty($this->creating_lessons_placeholders)) {
                    array_shift($this->creating_lessons_placeholders);
                    session(['lesson_placeholders_creating' => $this->creating_lessons_placeholders]);
                }
            }
        }

        // Trigger re-render - normal cleanup will happen in detectAndCleanPlaceholders()
        $this->resetPage();
    }

    /**
     * Handle lesson regeneration event from WebSocket
     * Removes placeholder for specific regenerated lesson
     *
     * @param  int|null  $lesson_database_id  - Database ID of regenerated lesson
     * @param  bool  $force_cleanup  - Force cleanup on error/exists
     */
    #[On('lesson-regenerated')]
    public function lessonRegenerated(?int $lesson_database_id = null, bool $force_cleanup = false): void
    {
        // Remove placeholder for specific regenerated lesson
        if ($lesson_database_id !== null && isset($this->regenerating_lessons_placeholders[$lesson_database_id])) {
            unset($this->regenerating_lessons_placeholders[$lesson_database_id]);
            session(['lesson_placeholders_regenerating' => $this->regenerating_lessons_placeholders]);
        }

        // Trigger re-render to refresh lesson data
        $this->resetPage();
    }

    /**
     * Detect new lessons and clean placeholders
     * Simple approach: if total count increased → remove placeholder
     *
     * @param  int  $current_total_count  - Total lessons count
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $lessons  - Paginated lessons
     */
    private function detectAndCleanPlaceholders(int $current_total_count, $lessons): void
    {
        // On FIRST RENDER: just initialize count, don't clean placeholders
        if ($this->previous_lessons_count === 0 && $current_total_count > 0) {
            $this->previous_lessons_count = $current_total_count;
            // Don't clean - user just loaded the page, these are existing lessons
        }
        // On SUBSEQUENT RENDERS: check if count increased
        elseif ($current_total_count > $this->previous_lessons_count) {
            $lessons_added = $current_total_count - $this->previous_lessons_count;

            // Remove placeholders for new lessons
            for ($i = 0; $i < $lessons_added; $i++) {
                if (! empty($this->creating_lessons_placeholders)) {
                    array_shift($this->creating_lessons_placeholders);
                }
            }

            if (! empty($this->creating_lessons_placeholders)) {
                session(['lesson_placeholders_creating' => $this->creating_lessons_placeholders]);
            } else {
                session()->forget('lesson_placeholders_creating');
            }

            // Update count after cleanup
            $this->previous_lessons_count = $current_total_count;
        }

        // Clean regenerating placeholders intelligently:
        // 1. Remove if lesson was updated AFTER placeholder creation (regeneration completed, WebSocket missed)
        // 2. Remove if placeholder is very old (>5 min) - stuck/stale
        $current_time = time();
        $max_placeholder_age = 300; // 5 minutes
        $current_page_lesson_ids = $lessons->pluck('id')->toArray();

        foreach ($this->regenerating_lessons_placeholders as $lesson_id => $placeholder_data) {
            $age = $current_time - $placeholder_data['timestamp'];

            // Check if lesson exists on current page
            if (in_array($lesson_id, $current_page_lesson_ids, true)) {
                // Get lesson's updated_at timestamp
                $lesson = $lessons->firstWhere('id', $lesson_id);

                if ($lesson && $lesson->updated_at) {
                    $lesson_updated_timestamp = $lesson->updated_at->timestamp;

                    // If lesson was updated AFTER placeholder was created, regeneration is complete
                    if ($lesson_updated_timestamp > $placeholder_data['timestamp']) {
                        unset($this->regenerating_lessons_placeholders[$lesson_id]);

                        continue;
                    }
                }
            }

            // Remove if placeholder is very old (5+ minutes) - likely stuck/error
            if ($age > $max_placeholder_age) {
                unset($this->regenerating_lessons_placeholders[$lesson_id]);
            }
        }

        // Update session if placeholders changed
        if (! empty($this->regenerating_lessons_placeholders)) {
            session(['lesson_placeholders_regenerating' => $this->regenerating_lessons_placeholders]);
        } else {
            session()->forget('lesson_placeholders_regenerating');
        }

        // Update count for next render
        $this->previous_lessons_count = $current_total_count;
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
                ->with(['topic' => function (Builder $q): void {
                    $q->with('translations');
                }]);
        } else {
            // Get user's own lessons
            $query = Lesson::where('user_id', '=', $user->id)
                ->with(['topic' => function (Builder $q): void {
                    $q->with('translations');
                }]);
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
        $this->show_manage_modal = true;
        // Dispatch event to reset manage lesson component state
        $this->dispatch('reset-manage-lesson-state');
    }

    public function closeManageModal(): void
    {
        $this->show_manage_modal = false;
        $this->js('document.body.style.overflow = "auto";');
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

            $user->notify(new LessonRegenerationNotification(
                $user,
                LessonRegenerationStatusEnum::PROCESSING,
                null,
            ));

            // Dispatch job for async lesson regeneration
            RegenerateLessonJob::dispatch(
                $this->lesson_to_regenerate,
                $lesson_dto
            )
                ->onQueue(QueueNameEnum::LESSON->value);

            // Add placeholder for regeneration (linked to lesson ID)
            // Placeholder updates immediately since we're in same component
            $placeholder_id = uniqid('regenerating_', true);
            $this->regenerating_lessons_placeholders[$this->lesson_to_regenerate] = [
                'id' => $placeholder_id,
                'timestamp' => time(),
            ];
            session(['lesson_placeholders_regenerating' => $this->regenerating_lessons_placeholders]);

        } catch (\Exception $e) {
            $user->notify(new LessonRegenerationNotification(
                $user,
                LessonRegenerationStatusEnum::ERROR,
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
            /** @var User $student */
            $student = User::find($student_id);

            if ($student) {
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
        $student->notify(new LessonUnassignedNotification($student, $lesson, $teacher->full_name));

        // Notify teacher (confirmation)
        $teacher->notify(new LessonAssignmentRemovedNotification($teacher, $lesson, $student->full_name));

        // Clear computed cache
        unset($this->assignedStudentIds);
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
        // Get total count BEFORE pagination
        $total_count = $this->getLessonsQuery()->count();

        // Get paginated lessons
        $lessons = $this->getLessonsQuery()->paginate(12);

        // Detect new lessons and clean placeholders
        $this->detectAndCleanPlaceholders($total_count, $lessons);

        return view('livewire.lesson.lesson-index', [
            'lessons' => $lessons,
        ]);
    }
}
