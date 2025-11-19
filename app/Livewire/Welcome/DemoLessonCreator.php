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
use Database\Seeders\GuestUserSeeder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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

    public function mount(): void
    {
        // Check if there's a pending lesson from previous session
        if (session('pending_demo_guest_user_id') && session('pending_demo_topic_id')) {
            $this->pending_lesson_id = -1; // Mark as pending
            $this->checkLessonStatus();
        }

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
        // Validate input
        $this->validate([
            'native_lang' => 'required|string|different:learning_lang',
            'learning_lang' => 'required|string|different:native_lang',
            'topic_id' => 'required|integer',
            'language_level' => 'required|string',
        ], [
            'native_lang.different' => __('lessons.languages_must_be_different'),
            'learning_lang.different' => __('lessons.languages_must_be_different'),
        ]);

        $this->reset(['is_generating', 'demo_lesson_url']);
        $this->is_generating = true;

        $topic = Topic::find($this->topic_id);
        if (! $topic) {
            $this->is_generating = false;
            $this->dispatch('show-error', message: __('lessons.topic_not_found'));

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
                $this->native_lang,
                $this->learning_lang,
                $keywords
            );

            // Get guest user ID
            $guest_user = \App\Models\User::where('email', GuestUserSeeder::GUEST_EMAIL)->firstOrFail();

            // Store tracking info in session to find the lesson later
            session([
                'pending_demo_guest_user_id' => $guest_user->id,
                'pending_demo_topic_id' => $this->topic_id,
                'pending_demo_started_at' => now()->timestamp,
            ]);

            // Mark that we're waiting for a lesson
            $this->pending_lesson_id = -1; // Placeholder to show spinner

            // Dispatch job for async lesson generation
            CreateLessonJob::dispatch(
                $guest_user->id,
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
     * Check if the pending lesson has been generated
     */
    public function checkLessonStatus(): void
    {
        if (! $this->pending_lesson_id) {
            return;
        }

        $guest_user_id = session('pending_demo_guest_user_id');
        $topic_id = session('pending_demo_topic_id');
        $started_at = session('pending_demo_started_at');

        if (! $guest_user_id || ! $topic_id) {
            return;
        }

        // Find the most recent lesson created by guest user for this topic since we started
        $lesson = Lesson::where('user_id', $guest_user_id)
            ->where('topic_id', $topic_id)
            ->when($started_at, function ($query) use ($started_at) {
                $query->where('created_at', '>=', \Carbon\Carbon::createFromTimestamp($started_at));
            })
            ->whereNotNull('lesson_text')
            ->whereNotNull('lesson_name')
            ->where('lesson_name', '!=', 'Generating...')
            ->latest()
            ->first();

        if ($lesson) {
            $this->demo_lesson_url = route('lessons.show', $lesson->id);
            $this->demo_lesson_name = $lesson->lesson_name;
            $this->show_success_message = true;
            $this->pending_lesson_id = null;
            
            // Clear session data
            session()->forget(['pending_demo_guest_user_id', 'pending_demo_topic_id', 'pending_demo_started_at']);
        }
    }

    public function render(): Factory|View
    {
        return view('livewire.welcome.demo-lesson-creator');
    }
}
