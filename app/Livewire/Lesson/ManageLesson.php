<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Enums\EntityTypeEnum;
use App\Dto\LessonDto;
use App\Enums\LanguageLevelEnum;
use App\Enums\LessonCreationStatusEnum;
use App\Jobs\Lesson\CreateLessonJob;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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

    /** @var array<int,int> */
    public array $keyword_ids = [];

    /** @var array<int,array{id:int,label:string}> */
    public array $keywords = [];

    public string $language_level = '';

    public bool $is_generating = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $locale = app()->getLocale();

        $this->preloaded_topics = Topic::with([
            'translations',
            'keywords' => function ($q) use ($user) {
                $q->visibleForUser($user->id);
            },
            'keywords.translations',
        ])
            ->visibleForUser($user->id)
            ->get();

        $this->topics = $this->preloaded_topics->map(function (Topic $topic) use ($locale) {
            return [
                'id' => $topic->id,
                'label' => $topic->title($locale),
            ];
        })->values()->toArray();

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

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = array_values(array_map('intval', (array) session('selected_keyword_ids', [])));

        $this->language_level = session('selected_language_level', LanguageLevelEnum::A1->value);

        session(['selected_topic_id' => $this->topic_id]);

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $this->keywords, reset: false, selectedIds: $this->keyword_ids);
    }

    public function updatedTopicId(int|string $value): void
    {
        $this->topic_id = (int) $value;
        session(['selected_topic_id' => $this->topic_id]);

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = [];
        session()->forget('selected_keyword_ids');

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $this->keywords, reset: true, selectedIds: []);

        $this->dispatch('topic-changed', id: $this->topic_id);
    }

    public function createLesson(): void
    {
        // Reset state
        $this->reset(['is_generating']);
        $this->is_generating = true;

        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $topic = Topic::find($this->topic_id);
        if (! $topic) {
            $this->notifyUser('error', __('lessons.topic_not_found'));
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
                $user->learning_lang,
                $keywords
            );

            // Dispatch job for async lesson creation
            CreateLessonJob::dispatch(
                $user->id,
                $this->topic_id,
                $lesson_dto,
                app()->getLocale()
            );

            // Send immediate notification that creation is in process
            $user->notify(new LessonCreationNotification(
                null,
                LessonCreationStatusEnum::PROCESSING
            ));

            // Reset generating state
            $this->is_generating = false;

            // Notify user via UI
            $this->notifyUser(LessonCreationStatusEnum::PROCESSING->value, __('lessons.lesson_creation_in_progress'));

            // Dispatch events and close modal
            $this->dispatch('lesson-creation-queued');
            $this->dispatch('close-manage-modal');

        } catch (\Exception $e) {
            $this->is_generating = false;
            $this->notifyUser('error', __('lessons.generation_failed').': '.$e->getMessage());

            // Log error for debugging
            $this->js('console.error("Lesson creation dispatch failed:", "'.addslashes($e->getMessage()).'");');
        }
    }

    protected function notifyUser(string $status, string $message): void
    {
        $notification_id = uniqid('lesson_');

        $this->dispatch('lesson-notification',
            id: $notification_id,
            entity: EntityTypeEnum::LESSON->value,
            status: $status,
            message: $message
        );

        // Debug log
        $this->js("console.log('Notification sent:', { id: '{$notification_id}', status: '{$status}', message: '{$message}' });");
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

    #[On('reset-manage-lesson-state')]
    public function resetState(): void
    {
        $this->reset(['is_generating']);
        $this->is_generating = false;

        // Debug log
        $this->js("console.log('ManageLesson state reset');");
    }

    /**
     * Build keyword options for the provided topic id.
     * Uses preloaded data to avoid additional DB queries.
     *
     * @return array<int, array{id:int,label:string}>
     */
    protected function buildKeywordsForTopic(?int $topic_id): array
    {
        if ($topic_id === null || $this->preloaded_topics === null) {
            return [];
        }

        $locale = app()->getLocale();

        $topic = $this->preloaded_topics->firstWhere('id', $topic_id);

        if (! $topic) {
            return [];
        }

        if (! $topic->relationLoaded('keywords')) {
            return [];
        }

        $keywords = $topic->keywords;

        if ($keywords->isEmpty()) {
            return [];
        }

        return $keywords->map(function (Keyword $keyword) use ($locale) {
            return [
                'id' => $keyword->id,
                'label' => $keyword->value($locale),
            ];
        })->values()->toArray();
    }

    public function render(): Factory|View
    {
        return view('livewire.lesson.manage-lesson');
    }
}
