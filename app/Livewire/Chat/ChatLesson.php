<?php
declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Dto\LessonDto;
use App\Enums\AvailableTopicEnum;
use App\Models\Lesson;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Livewire\Component;


class ChatLesson extends Component
{
    public array $messages = [];
    public string $input = '';
    public string $topic = '';
    public int $topic_id;
    public ?int $lesson_id = null;

    /** @var array<int,array{id:int,label:string}> */
    public array $topics = [];

    public function mount(): void
    {
        $this->messages = [];

        $this->topics = collect(AvailableTopicEnum::cases())
            ->map(fn($case) => [
                'id' => $case->value,
                'label' => Lang::get('topics.' . $case->name, [], app()->getLocale()),
            ])
            ->sortBy('label')
            ->values()
            ->all();

        if (!empty($this->topics)) {
            $this->topic_id = $this->topics[0]['id'];
            $this->topic = $this->topics[0]['label'];
        }
    }

    public function updatedTopicId($value): void
    {
        foreach ($this->topics as $topic) {
            if ((int)$topic['id'] === (int)$value) {
                $this->topic = $topic['label'];
                break;
            }
        }
    }

    public function startLesson(OpenAIService $openAIService): void
    {
        $user = Auth::user();

        $lessonDto = new LessonDto(
            $this->topic_id,
            'beginner',
            $user->native_lang_id,
            $user->learning_lang_id,
            [$this->topic]
        );

        $data = $openAIService->generateLesson($lessonDto);

        $this->messages = [
            ['sender' => 'ai', 'content' => $data['lesson_text'] ?? 'Hello!'],
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
        if (!trim($this->input)) {
            return;
        }

        $userMessage = trim($this->input);
        $this->messages[] = ['sender' => 'user', 'content' => $userMessage];
        $this->input = '';

        $this->messages[] = ['sender' => 'ai', 'content' => 'Thanks — got it!'];
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.chat-lesson');
    }
}
