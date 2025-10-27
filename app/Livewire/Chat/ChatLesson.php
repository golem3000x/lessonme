<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Dto\LessonDto;
use App\Models\Keyword\Keyword;
use App\Models\Lesson;
use App\Models\Topic\Topic;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatLesson extends Component
{
    public array $messages = [];

    public string $input = '';

    public string $topic = '';

    public ?int $topic_id = null;

    public ?int $lesson_id = null;

    public $preloaded_topics;

    /** @var array<int,array{id:int,label:string}> */
    public array $topics = [];

    /** @var array<int,int> */
    public array $keyword_ids = [];

    public function mount(): void
    {
        $user = Auth::user();
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

        $this->topic_id ??= session('selected_topic_id');

        if (! $this->topic_id && ! empty($this->topics)) {
            $this->topic_id = $this->topics[0]['id'];
        }

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = array_values(array_map('intval', (array) session('selected_keyword_ids', [])));

        session(['selected_topic_id' => $this->topic_id]);

        $preloaded = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $preloaded, reset: false, selectedIds: $this->keyword_ids);
    }

    public function updatedTopicId(int|string $value): void
    {
        $this->topic_id = (int) $value;
        session(['selected_topic_id' => $this->topic_id]);

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';

        $this->keyword_ids = [];
        session()->forget('selected_keyword_ids');

        $preloaded = $this->buildKeywordsForTopic($this->topic_id);
        $this->dispatch('keywords-preloaded', keywords: $preloaded, reset: true, selectedIds: []);

        $this->dispatch('topic-changed', id: $this->topic_id);
    }

    public function startLesson(OpenAIService $openAIService): void
    {
        $user = Auth::user();

        $topic = Topic::find($this->topic_id);
        if (! $topic) {
            $this->dispatch('error', message: __('chat.topic_not_found'));

            return;
        }

        $selected_keyword_labels = [];

        if (! empty($this->keyword_ids)) {
            $keywords = Keyword::with('translations')->whereIn('id', $this->keyword_ids)->get();
            $locale = app()->getLocale();
            $selected_keyword_labels = $keywords->map(fn (Keyword $k) => $k->value($locale))->filter()->values()->all();
        }

        $prompts = ! empty($selected_keyword_labels) ? $selected_keyword_labels : [$this->topic];

        $lessonDto = new LessonDto(
            $this->topic,
            'beginner',
            $user->native_lang,
            $user->learning_lang,
            $prompts
        );

        $data = $openAIService->generateLesson($lessonDto);

        $this->messages = [
            ['sender' => 'ai', 'content' => $data['lesson_text'] ?? __('chat.default_message')],
        ];

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'topic_id' => $this->topic_id,
            'lesson_name' => $data['lesson_name'] ?? '',
            'lesson_text' => $data['lesson_text'] ?? '',
            'grammar_data' => $data['grammar_highlights'] ?? [],
            'vocabulary' => $data['vocabulary'] ?? [],
            'quiz' => $data['quiz'] ?? [],
        ]);

        $this->lesson_id = $lesson->id;
    }

    public function send(): void
    {
        if (! trim($this->input)) {
            return;
        }

        $user_message = trim($this->input);
        $this->messages[] = ['sender' => 'user', 'content' => $user_message];
        $this->input = '';

        $this->messages[] = ['sender' => 'ai', 'content' => __('chat.response_acknowledged')];
    }

    #[On('keywords-selected')]
    public function setKeywordIds(array $ids): void
    {
        $this->keyword_ids = array_values(array_unique(array_map('intval', $ids)));
        session(['selected_keyword_ids' => $this->keyword_ids]);
    }

    /**
     * Build keyword options for the provided topic id.
     *
     * @return array<int, array{id:int,label:string}>
     */
    protected function buildKeywordsForTopic(?int $topicId): array
    {
        if ($topicId === null) {
            return [];
        }

        $user = Auth::user();
        $locale = app()->getLocale();

        $keywords = Keyword::with('translations')
            ->where('topic_id', '=', $topicId)
            ->visibleForUser($user->id)
            ->get();

        return $keywords->map(function (Keyword $keyword) use ($locale) {
            return [
                'id' => $keyword->id,
                'label' => $keyword->value($locale),
            ];
        })->values()->toArray();
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.chat-lesson');
    }
}
