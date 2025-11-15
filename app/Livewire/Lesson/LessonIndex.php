<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Dto\LessonDto;
use App\Enums\LanguageLevelEnum;
use App\Enums\LessonCreationStatusEnum;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Lesson\Lesson;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonIndex extends Component
{
    /** @var Collection<int, Lesson> */
    public Collection $lessons;

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

    public function __construct()
    {
        $this->lessons = new Collection;
    }

    public function mount(): void
    {
        // Initialize temp filters with applied values
        $this->temp_search = $this->search;
        $this->temp_language_levels = $this->language_levels;
        $this->temp_sort = $this->sort;
        
        $this->loadLessons();
    }

    #[On('lesson-created')]
    #[On('lesson-regenerated')]
    public function loadLessons(): void
    {
        $query = Lesson::where('user_id', Auth::id())
            ->with(['topicRelation' => function ($q) {
                $q->with('translations');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where('lesson_name', 'like', '%'.$this->search.'%');
        }

        // Apply language level filter
        if (!empty($this->language_levels)) {
            $query->whereIn('language_level', $this->language_levels);
        }

        // Apply sorting
        if ($this->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $this->lessons = $query->get();
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
            ->with(['topicRelation.translations', 'topicRelation.keywords.translations'])
            ->first();

        if (! $lesson) {
            $this->closeDropdown($lesson_id);
            session()->flash('error', __('lessons.lesson_not_found'));

            return;
        }

        try {
            $locale = app()->getLocale();
            $topic_name = $lesson->topicRelation?->title($locale) ?? '';

            // Get keywords from topic
            $keywords = [];
            if ($lesson->topicRelation && $lesson->topicRelation->relationLoaded('keywords')) {
                $keywords = $lesson->topicRelation->keywords
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
                LessonCreationStatusEnum::PROCESSING->value,
                __('lessons.lesson_regeneration_in_progress')
            ));

            // Close dropdown
            $this->closeDropdown($lesson_id);

            // Notify user via Livewire dispatch
            $this->dispatch('lesson-notification',
                id: uniqid('lesson_regenerate_'),
                entity: 'lesson',
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
            $this->loadLessons();
            session()->flash('message', __('lessons.lesson_deleted'));
        }
    }

    public function toggleFilters(): void
    {
        $this->show_filters = ! $this->show_filters;
        
        // Close level dropdown when closing filters panel
        if (!$this->show_filters) {
            $this->show_level_dropdown = false;
        }
    }

    public function applyFilters(): void
    {
        $this->search = $this->temp_search;
        $this->language_levels = $this->temp_language_levels;
        $this->sort = $this->temp_sort;
        
        $this->loadLessons();
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
        
        $this->loadLessons();
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
        if (!empty($this->temp_language_levels)) {
            $query->whereIn('language_level', $this->temp_language_levels);
        }

        return $query->count();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || !empty($this->language_levels) || $this->sort !== 'newest';
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
        return view('livewire.lesson.lesson-index');
    }
}
