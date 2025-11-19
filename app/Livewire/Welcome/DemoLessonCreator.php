<?php

declare(strict_types=1);

namespace App\Livewire\Welcome;

use App\Dto\LessonDto;
use App\Enums\LanguageLevelEnum;
use App\Enums\SystemLanguageEnum;
use App\Jobs\Lesson\CreateLessonJob;
use App\Models\Keyword\Keyword;
use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class DemoLessonCreator extends Component
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

    public string $native_lang = '';

    public string $learning_lang = '';

    public bool $is_generating = false;

    public ?string $demo_lesson_url = null;

    public ?string $demo_lesson_name = null;

    public ?int $pending_lesson_id = null;

    public bool $show_success_message = false;

    public bool $show_error_message = false;

    public ?string $error_message = null;

    public function mount(): void
    {
        $locale = app()->getLocale();

        // Load system topics and keywords
        $this->preloaded_topics = Topic::with([
            'translations',
            'keywords.translations',
        ])
            ->where('is_system', true)
            ->where('is_approved', true)
            ->get();

        $this->topics = $this->preloaded_topics->map(function (Topic $topic) use ($locale) {
            return [
                'id' => $topic->id,
                'label' => $topic->title($locale),
            ];
        })->values()->toArray();

        // Set default values
        if (! empty($this->topics)) {
            $this->topic_id = $this->topics[0]['id'];
            $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
            $this->topic = $selected['label'] ?? '';
        }

        $this->language_level = LanguageLevelEnum::A1->value;
        $this->native_lang = SystemLanguageEnum::VIETNAMESE->value;
        $this->learning_lang = SystemLanguageEnum::ENGLISH->value;

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $this->keywords, reset: false, selectedIds: $this->keyword_ids);

        // Check if temp guest already has a lesson
        $temp_guest_user_id = session('temp_guest_user_id');
        if ($temp_guest_user_id) {
            $existing_lesson = Lesson::where('user_id', '=', $temp_guest_user_id)
                ->first();

            if ($existing_lesson) {
                // Show existing lesson button immediately
                $this->demo_lesson_url = route('lessons.show', $existing_lesson->id);
                $this->demo_lesson_name = $existing_lesson->lesson_name;
                $this->show_success_message = true;
            }
        }
    }

    public function updatedTopicId(int|string $value): void
    {
        $this->topic_id = (int) $value;

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = [];

        $this->keywords = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $this->keywords, reset: true, selectedIds: []);
    }

    public function createDemoLesson(): void
    {
        // Validate input with comprehensive rules
        $valid_levels = implode(',', LanguageLevelEnum::values());

        $this->validate([
            'native_lang' => 'required|string|in:en,vi|different:learning_lang',
            'learning_lang' => 'required|string|in:en,vi|different:native_lang',
            'topic_id' => 'required|integer|exists:topics,id',
            'language_level' => 'required|string|in:'.$valid_levels,
            'keyword_ids' => 'nullable|array',
            'keyword_ids.*' => 'integer|exists:keywords,id',
        ], [
            'native_lang.required' => __('validation.required', ['attribute' => __('lessons.native_language')]),
            'native_lang.in' => __('validation.in', ['attribute' => __('lessons.native_language')]),
            'native_lang.different' => __('lessons.languages_must_be_different'),
            'learning_lang.required' => __('validation.required', ['attribute' => __('lessons.target_language')]),
            'learning_lang.in' => __('validation.in', ['attribute' => __('lessons.target_language')]),
            'learning_lang.different' => __('lessons.languages_must_be_different'),
            'topic_id.required' => __('validation.required', ['attribute' => __('lessons.topic')]),
            'topic_id.exists' => __('lessons.topic_not_found'),
            'language_level.required' => __('validation.required', ['attribute' => __('lessons.level')]),
            'language_level.in' => __('validation.in', ['attribute' => __('lessons.level')]),
            'keyword_ids.*.exists' => __('lessons.keyword_not_found'),
        ]);

        $this->reset(['is_generating', 'demo_lesson_url']);
        $this->is_generating = true;

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
                $this->native_lang,
                $this->learning_lang,
                $keywords
            );

            // Get or create temporary guest user for this session
            $temp_guest_user_id = $this->ensureTempGuestUser();

            // Use microsecond precision timestamp as unique identifier for this creation request
            $creation_timestamp = microtime(true);

            // Store tracking info in session to find the lesson later
            session([
                'pending_demo_guest_user_id' => $temp_guest_user_id,
                'pending_demo_topic_id' => $this->topic_id,
                'pending_demo_started_at' => $creation_timestamp,
            ]);

            // Reset error state
            $this->reset(['show_error_message', 'error_message', 'show_success_message']);

            // Mark that we're waiting for a lesson
            $this->pending_lesson_id = -1; // Placeholder to show spinner

            // Dispatch job for async lesson generation
            CreateLessonJob::dispatch(
                $temp_guest_user_id,
                $this->topic_id,
                $lesson_dto,
                app()->getLocale()
            );

            $this->is_generating = false;

        } catch (\Exception $e) {
            $this->is_generating = false;
            $this->dispatch('show-error', message: __('lessons.generation_failed').': '.$e->getMessage());
        }
    }

    /**
     * @param  array<int, int>  $ids
     */
    #[On('keywords-selected')]
    public function setKeywordIds(array $ids): void
    {
        $this->keyword_ids = array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * Build keyword options for the provided topic id.
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

    /**
     * Handle lesson creation notification from WebSocket
     * Called when Echo.js receives lesson creation event
     */
    #[On('lesson-notification')]
    public function handleLessonNotification(array $notification): void
    {
        Log::info('DemoLessonCreator received notification', $notification);

        $status = $notification['status'] ?? null;

        if ($status === 'successfully') {
            // Lesson created successfully
            $this->demo_lesson_url = $notification['lesson_url'] ?? null;
            $this->demo_lesson_name = $notification['lesson_name'] ?? null;
            $this->show_success_message = true;
            $this->pending_lesson_id = null;
            $this->is_generating = false;

            // Clear error state
            $this->reset(['show_error_message', 'error_message']);

            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);
        } elseif ($status === 'exists') {
            // Lesson already exists
            $this->demo_lesson_url = $notification['lesson_url'] ?? null;
            $this->demo_lesson_name = $notification['lesson_name'] ?? null;
            $this->show_success_message = true;
            $this->pending_lesson_id = null;
            $this->is_generating = false;

            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);
        } elseif ($status === 'error') {
            // Lesson creation failed
            $this->pending_lesson_id = null;
            $this->show_error_message = true;
            $this->error_message = $notification['message'] ?? __('lessons.creation_failed');
            $this->is_generating = false;

            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);
        }
    }

    /**
     * Retry lesson creation after error
     */
    public function retryCreation(): void
    {
        $this->reset(['show_error_message', 'error_message', 'show_success_message', 'pending_lesson_id']);
        $this->createDemoLesson();
    }

    /**
     * Check if lesson has been created (polling method)
     * Called every 3 seconds when lesson is being created
     */
    public function checkLessonStatus(): void
    {
        // Only check if we're waiting for a lesson
        if (! $this->pending_lesson_id) {
            return;
        }

        $temp_guest_user_id = session('temp_guest_user_id');
        if (! $temp_guest_user_id) {
            return;
        }

        // Check timeout: if more than 3 minutes passed, show error
        $started_at = session('pending_demo_started_at');
        if ($started_at && (microtime(true) - $started_at) > 180) {
            // Timeout: 3 minutes
            $this->pending_lesson_id = null;
            $this->show_error_message = true;
            $this->error_message = __('lessons.creation_timeout');
            $this->is_generating = false;

            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);

            return;
        }

        // Check if lesson has been created
        $lesson = Lesson::where('user_id', '=', $temp_guest_user_id)
            ->latest()
            ->first();

        if ($lesson) {
            // Lesson created successfully!
            $this->demo_lesson_url = route('lessons.show', $lesson->id);
            $this->demo_lesson_name = $lesson->lesson_name;
            $this->show_success_message = true;
            $this->pending_lesson_id = null;
            $this->is_generating = false;

            // Clear error state
            $this->reset(['show_error_message', 'error_message']);

            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);
        }
    }

    /**
     * Get temporary guest user ID from session
     * Middleware already created it before page render
     *
     * @return int User ID of the temp guest user
     */
    private function ensureTempGuestUser(): int
    {
        $temp_guest_user_id = session('temp_guest_user_id');

        if (! $temp_guest_user_id) {
            throw new \RuntimeException('Temp guest user not found in session. Middleware should have created it.');
        }

        return $temp_guest_user_id;
    }

    public function render(): Factory|View
    {
        return view('livewire.welcome.demo-lesson-creator');
    }
}
