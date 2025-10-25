<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Dto\LessonDto;
use App\Models\Lesson;
use App\Models\Topic\Topic;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatLesson extends Component
{
    public $messages = [];

    public $input = '';

    public $topic = '';

    public $topic_id = null;

    public $lesson_id = null;

    /** @var array<int,array{id:int,label:string}> */
    public array $topics = [];

    public function mount(): void
    {
        $user = Auth::user();
        $locale = app()->getLocale();

        $topics = Topic::with('translations')
            ->visibleForUser($user->id)
            ->get();

        $this->topics = $topics->map(function (Topic $topic) use ($locale) {
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

        session(['selected_topic_id' => $this->topic_id]);
    }

    public function updatedTopicId(int|string $value): void
    {
        $this->topic_id = (int) $value;
        session(['selected_topic_id' => $this->topic_id]); // 💾 сохраняем выбор

        $selected = collect($this->topics)->firstWhere('id', $this->topic_id);
        $this->topic = $selected['label'] ?? '';
    }

    public function startLesson(OpenAIService $openAIService): void
    {
        $user = Auth::user();

        $topic = Topic::find($this->topic_id);
        if (! $topic) {
            $this->dispatch('error', message: __('chat.topic_not_found'));

            return;
        }

        $lessonDto = new LessonDto(
            $this->topic,
            'beginner',
            $user->native_lang,
            $user->learning_lang,
            [$this->topic]
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

        $userMessage = trim($this->input);
        $this->messages[] = ['sender' => 'user', 'content' => $userMessage];
        $this->input = '';

        $this->messages[] = ['sender' => 'ai', 'content' => __('chat.response_acknowledged')];
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.chat-lesson');
    }
}
