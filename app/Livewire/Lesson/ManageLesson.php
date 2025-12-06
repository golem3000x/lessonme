<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

/**
 * ManageLesson component for creating new lessons
 *
 * PLACEHOLDER FLOW:
 * 1. Add placeholder to SESSION (persists across page reloads)
 * 2. Dispatch 'placeholder-added' event to LessonIndex (real-time UI update)
 *
 * This dual approach ensures:
 * - Placeholder shows immediately (via event)
 * - Placeholder persists if user navigates away (via session)
 * - PlaceholderManager cleans up via WebSocket
 */

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\QueueNameEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Lesson\CreateLessonJob;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageLesson extends Component
{
    public string $topic = '';

    public ?int $topic_id = null;

    /** @var Collection<int, Topic>|null */
    public ?Collection $preloaded_topics = null;

    /** @var array<int,array{id:int,label:string}> */
    public array $topics = [];

    /** @var array<int,array<int,array{id:int,label:string}>> */
    public array $all_keywords_by_topic = [];

    /** @var array<int,int> */
    public array $keyword_ids = [];

    /** @var array<int,array{id:int,label:string}> */
    public array $keywords = [];

    public string $language_level = '';

    public string $lesson_language = '';

    /** @var array<int,array{value:string,label:string,flag:string}> */
    public array $available_languages = [];

    public bool $is_generating = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $locale = app()->getLocale();

        $this->preloaded_topics = Topic::with([
            'translations',
            'keywords' => function (Builder $q) use ($user): void {
                $q->visibleForUser($user->id);
            },
            'keywords.translations',
        ])
            ->visibleForUser($user->id)
            ->get();

        $this->topics = $this->preloaded_topics->map(function (Topic $topic) use ($locale): array {
            return [
                'id' => $topic->id,
                'label' => $topic->title($locale),
            ];
        })->values()->toArray();

        // Preload all keywords into array to survive Livewire serialization
        foreach ($this->preloaded_topics as $topic) {
            $keywords_array = [];
            if ($topic->relationLoaded('keywords')) {
                $keywords_array = $topic->keywords->map(function (Keyword $keyword) use ($locale) {
                    return [
                        'id' => $keyword->id,
                        'label' => $keyword->value($locale),
                    ];
                })->values()->toArray();
            }
            $this->all_keywords_by_topic[$topic->id] = $keywords_array;
        }

        // Get topic_id from session or use first available
        $session_topic_id = session('selected_topic_id');

        // Check if session topic exists in available topics
        if ($session_topic_id) {
            $session_topic_exists = collect($this->topics)->contains('id', $session_topic_id);
            if ($session_topic_exists) {
                $this->topic_id = $session_topic_id;
            }
        }

        // If no valid topic_id, use first available
        if (! $this->topic_id && ! empty($this->topics)) {
            $this->topic_id = $this->topics[0]['id'];
        }

        $selected = collect($this->topics)->firstWhere('id', '=', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = array_values(array_map('intval', (array) session('selected_keyword_ids', [])));

        $this->language_level = session('selected_language_level', LanguageLevelEnum::A1->value);

        // Initialize available languages from user's learning languages
        $learning_languages = $user->getLearningLanguageValues();
        if (empty($learning_languages)) {
            // Fallback: use all languages if user has no learning languages set
            $learning_languages = SystemLanguageEnum::values();
        }

        $this->available_languages = collect($learning_languages)
            ->map(function (string $lang_value) {
                $enum = SystemLanguageEnum::tryFrom($lang_value);
                return $enum ? [
                    'value' => $enum->value,
                    'label' => $enum->label(),
                    'flag' => $enum->flag(),
                ] : null;
            })
            ->filter()
            ->values()
            ->toArray();

        // Get lesson language from session or use first available
        $session_lang = session('selected_lesson_language');
        if ($session_lang && in_array($session_lang, $learning_languages, true)) {
            $this->lesson_language = $session_lang;
        } elseif (! empty($this->available_languages)) {
            $this->lesson_language = $this->available_languages[0]['value'];
        }

        session(['selected_topic_id' => $this->topic_id]);

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded',
            keywords: $this->keywords,
            reset: false,
            selectedIds: $this->keyword_ids
        )->to('lesson.keyword-select');
    }

    public function updatedTopicId(int|string $value): void
    {
        $this->topic_id = (int) $value;
        session(['selected_topic_id' => $this->topic_id]);

        $selected = collect($this->topics)->firstWhere('id', '=', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = [];
        session()->forget('selected_keyword_ids');

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);

        $this->dispatch('keywords-preloaded',
            keywords: $this->keywords,
            reset: true,
            selectedIds: []
        )->to('lesson.keyword-select');

        $this->dispatch('topic-changed');
    }

    public function createLesson(): void
    {
        // Reset state
        $this->reset(['is_generating']);
        $this->is_generating = true;

        $user = Auth::user();
        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $topic = Topic::find($this->topic_id);
        if (! $topic) {
            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::ERROR,
                null,
            ));
            $this->is_generating = false;

            return;
        }

        try {
            $selected_keyword_labels = [];

            if (! empty($this->keyword_ids)) {
                $keywords = Keyword::with('translations')->whereIn('id', $this->keyword_ids)->get();
                $locale = app()->getLocale();
                $selected_keyword_labels = $keywords->map(fn (Keyword $k) => $k->value($locale))->filter()->values()->all();
            }

            $keywords = ! empty($selected_keyword_labels) ? $selected_keyword_labels : [$this->topic];
            $level_enum = LanguageLevelEnum::from($this->language_level);
            $lesson_dto = new LessonDto(
                $this->topic,
                $level_enum,
                $user->native_lang,
                $this->lesson_language,
                $keywords
            );

            // Ensure topic_id is not null (already validated above)
            if ($this->topic_id === null) {
                throw new \RuntimeException('Topic ID is required');
            }

            // Dispatch job for async lesson creation
            CreateLessonJob::dispatch(
                $user->id,
                $this->topic_id,
                $lesson_dto
            )
                ->onQueue(QueueNameEnum::LESSON->value);

            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::PROCESSING,
                null,
            ));

            // Add placeholder directly to session (works even if LessonIndex is not loaded)
            $creating_placeholders = session('lesson_placeholders_creating', []);
            $placeholder_id = uniqid('creating_', true);
            $creating_placeholders[] = $placeholder_id;
            session(['lesson_placeholders_creating' => $creating_placeholders]);

            // Notify LessonIndex to add placeholder to local state (for real-time update)
            $this->dispatch('placeholder-added', placeholder_id: $placeholder_id);

            // Reset generating state and close modal
            $this->is_generating = false;
            $this->dispatch('close-manage-modal');

        } catch (\Exception $e) {
            $this->is_generating = false;

            $user->notify(new LessonCreationNotification(
                $user,
                LessonCreationStatusEnum::ERROR,
                null,
            ));

            // Log error for debugging
            $this->js('console.error("Lesson creation dispatch failed:", "'.addslashes($e->getMessage()).'");');
        }
    }

    /**
     * @param  array<int, int>  $ids
     */
    #[On('keywords-selected')]
    public function setKeywordIds(array $ids): void
    {
        $this->keyword_ids = array_values(array_unique(array_map('intval', $ids)));
        session(['selected_keyword_ids' => $this->keyword_ids]);
    }

    public function updatedLanguageLevel(string $value): void
    {
        $this->language_level = $value;
        session(['selected_language_level' => $this->language_level]);
    }

    public function updatedLessonLanguage(string $value): void
    {
        $this->lesson_language = $value;
        session(['selected_lesson_language' => $this->lesson_language]);
    }

    #[On('reset-manage-lesson-state')]
    public function resetState(): void
    {
        $this->reset(['is_generating']);
        $this->is_generating = false;
    }

    /**
     * Build keyword options for the provided topic id.
     * Uses preloaded data to avoid additional DB queries.
     *
     * @return array<int, array{id:int,label:string}>
     */
    protected function buildKeywordsForTopic(?int $topic_id): array
    {
        if ($topic_id === null) {
            return [];
        }

        // Use preloaded array instead of Collection
        if (! isset($this->all_keywords_by_topic[$topic_id])) {
            return [];
        }

        return $this->all_keywords_by_topic[$topic_id];
    }

    public function render(): Factory|View
    {
        return view('livewire.lesson.manage-lesson');
    }
}
