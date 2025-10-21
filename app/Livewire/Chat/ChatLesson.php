<?php
declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Dto\LessonDto;
use App\Models\Lesson;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatLesson extends Component
{
    public $messages = [];
    public $input = '';
    public $topic = 'General';
    public $lessonId;

    public function mount(): void
    {
        $this->messages = [];
    }

    public function startLesson(OpenAIService $openAIService): void
    {
        $user = Auth::user();

        $lessonDto = new LessonDto(
            $this->topic,
            'beginner',
            $user->native_lang,
            $user->learning_lang,
            [$this->topic] ?? []
        );

        $data = $openAIService->generateLesson($lessonDto);
        $this->messages = [
            ['sender' => 'ai', 'content' => $data['lesson_text'] ?? 'Hello!'],
        ];
        /** @var Lesson $lesson */
        $lesson = Lesson::create([
            'user_id' => Auth::id(),
            'topic' => $data['topic'] ?? $this->theme,
            'lesson_text' => $data['lesson_text'] ?? '',
            'grammar_data' => $data['grammar_highlights'] ?? [],
            'vocabulary' => $data['vocabulary'] ?? [],
            'quiz' => $data['quiz'] ?? [],
        ]);
        $this->lessonId = $lesson->id;
    }

    public function send(): void
    {
        if (!trim($this->input)) return;
        $userMsg = trim($this->input);
        $this->messages[] = ['sender' => 'user', 'content' => $userMsg];
        $this->input = '';
        // lightweight echo reply for demo
        $this->messages[] = ['sender' => 'ai', 'content' => 'Thanks — got it!'];
    }

    public function render(): Factory|View|\View
    {
        return view('livewire.chat.chat-lesson');
    }
}
