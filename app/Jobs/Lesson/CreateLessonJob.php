<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Models\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\OpenAi\OpenAIService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $user_id,
        protected int $topic_id,
        protected LessonDto $lesson_dto
    ) {}

    public function handle(OpenAIService $open_ai_service): void
    {
        $user = User::find($this->user_id);

        if (! $user) {
            Log::error('User not found for lesson creation', ['user_id' => $this->user_id]);

            return;
        }

        try {
            // Generate lesson data via OpenAI
            $data = $open_ai_service->generateLesson($this->lesson_dto);

            // Create lesson
            $lesson = Lesson::create([
                'user_id' => $this->user_id,
                'topic_id' => $this->topic_id,
                'lesson_name' => $data['lesson_name'] ?? '',
                'lesson_text' => $data['lesson_text'] ?? '',
                'grammar_data' => $data['grammar_highlights'] ?? [],
                'vocabulary' => $data['vocabulary'] ?? [],
                'quiz' => $data['quiz'] ?? [],
            ]);

            // Notify user of successful creation with link
            $user->notify(new LessonCreationNotification(
                $lesson,
                'success',
                __('lessons.lesson_created_successfully', ['name' => $lesson->lesson_name])
            ));

        } catch (Exception $e) {
            Log::error('Lesson creation failed', [
                'user_id' => $this->user_id,
                'topic_id' => $this->topic_id,
                'error' => $e->getMessage(),
            ]);

            // Notify user of failure
            $user->notify(new LessonCreationNotification(
                null,
                'error',
                __('lessons.lesson_creation_failed')
            ));
        }
    }
}
