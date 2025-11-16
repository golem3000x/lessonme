<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Enums\EntityTypeEnum;
use App\Dto\LessonDto;
use App\Enums\LanguageLevelEnum;
use App\Enums\LessonCreationStatusEnum;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Lesson\Lesson;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    // Filters UI state
    public bool $show_filters = false;

    public bool $show_level_dropdown = false;

    // Filter values (temporary, not applied yet)
    public string $temp_search = '';

    /** @var array<int, string> */
    public array $temp_language_levels = [];

    public string $temp_sort = 'newest';

    // Applied filter values
    public string $search = '';

    /** @var array<int, string> */
    public array $language_levels = [];

    public string $sort = 'newest';

    public function mount(): void
    {
        // Restore filters from session
        $this->search = session('lesson_filters.search', '');
        $this->language_levels = session('lesson_filters.language_levels', []);
        $this->sort = session('lesson_filters.sort', 'newest');

        // Initialize temp filters with applied values
        $this->temp_search = $this->search;
        $this->temp_language_levels = $this->language_levels;
        $this->temp_sort = $this->sort;
    }

    #[On('lesson-created')]
    #[On('lesson-regenerated')]
    public function refreshLessons(): void
    {
        // Just refresh the component - pagination will be handled automatically
        $this->resetPage();
    }

    protected function getLessonsQuery()
    {
        $query = Lesson::where('user_id', Auth::id())
            ->with(['topic' => function ($q) {
                $q->with('translations');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where('lesson_name', 'like', '%'.$this->search.'%');
        }

        // Apply language level filter
        if (! empty($this->language_levels)) {
            $query->whereIn('language_level', $this->language_levels);
        }

        // Apply sorting
        if ($this->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        return $query;
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
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $lesson = Lesson::where('id', $lesson_id)
            ->where('user_id', $user->id)
            ->with(['topic.translations', 'topic.keywords.translations'])
            ->first();

        if (! $lesson) {
            $this->closeDropdown($lesson_id);
            session()->flash('error', __('lessons.lesson_not_found'));

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
                $user->learning_lang,
                $keywords
            );

            // Dispatch job for async lesson regeneration
            RegenerateLessonJob::dispatch(
                $lesson_id,
                $lesson_dto,
                app()->getLocale()
            );

            // Send immediate notification that regeneration is in process
            $user->notify(new LessonCreationNotification(
                null,
                LessonCreationStatusEnum::PROCESSING
            ));

            // Close dropdown
            $this->closeDropdown($lesson_id);

            // Notify user via Livewire dispatch
            $this->dispatch('lesson-notification',
                id: uniqid('lesson_regenerate_'),
                entity: EntityTypeEnum::LESSON->value,
                status: LessonCreationStatusEnum::PROCESSING->value,
                message: __('lessons.lesson_regeneration_in_progress')
            );

        } catch (\Exception $e) {
            $this->closeDropdown($lesson_id);
            session()->flash('error', __('lessons.regeneration_failed').': '.$e->getMessage());
        }
    }

    public function deleteLesson(int $lesson_id): void
    {
        $lesson = Lesson::where('id', $lesson_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($lesson) {
            $lesson->delete();
            $this->resetPage();
            session()->flash('message', __('lessons.lesson_deleted'));
        }
    }

    public function toggleFilters(): void
    {
        $this->show_filters = ! $this->show_filters;

        // Sync temp filters with current applied filters when opening
        if ($this->show_filters) {
            $this->temp_search = $this->search;
            $this->temp_language_levels = $this->language_levels;
            $this->temp_sort = $this->sort;
        }

        // Close level dropdown when closing filters panel
        if (! $this->show_filters) {
            $this->show_level_dropdown = false;
        }
    }

    public function applyFilters(): void
    {
        $this->search = $this->temp_search;
        $this->language_levels = $this->temp_language_levels;
        $this->sort = $this->temp_sort;

        // Save filters to session
        session([
            'lesson_filters.search' => $this->search,
            'lesson_filters.language_levels' => $this->language_levels,
            'lesson_filters.sort' => $this->sort,
        ]);

        $this->resetPage();
        $this->show_filters = false;
        $this->show_level_dropdown = false;
    }

    public function resetFilters(): void
    {
        $this->temp_search = '';
        $this->temp_language_levels = [];
        $this->temp_sort = 'newest';

        $this->search = '';
        $this->language_levels = [];
        $this->sort = 'newest';

        // Clear filters from session
        session()->forget('lesson_filters');

        $this->resetPage();
        $this->show_level_dropdown = false;
    }

    #[Computed]
    public function filteredLessonsCount(): int
    {
        $query = Lesson::where('user_id', Auth::id());

        // Apply temporary search filter
        if ($this->temp_search) {
            $query->where('lesson_name', 'like', '%'.$this->temp_search.'%');
        }

        // Apply temporary language level filter
        if (! empty($this->temp_language_levels)) {
            $query->whereIn('language_level', $this->temp_language_levels);
        }

        return $query->count();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || ! empty($this->language_levels) || $this->sort !== 'newest';
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

    public function render(): Factory|View
    {
        $lessons = $this->getLessonsQuery()->paginate(12);

        return view('livewire.lesson.lesson-index', [
            'lessons' => $lessons,
        ]);
    }
}
