<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Enums\LessonCreationStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\OpenAi\EmbeddingService;
use App\Services\OpenAi\OpenAIService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CreateLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private OpenAIService $open_ai_service;

    public function __construct(
        protected int       $user_id,
        protected int       $topic_id,
        protected LessonDto $lesson_dto
    )
    {
        $this->open_ai_service = App::make(OpenAIService::class);
    }

    public function handle(): void
    {
        $user = User::find($this->user_id);

        if (!$user) {
            Log::error('User not found for lesson creation', ['user_id' => $this->user_id]);

            return;
        }

        try {
            // Generate embedding
            $embedding = $this->open_ai_service->createEmbedding($this->lesson_dto);
            if (!$embedding) {
                $user->notify(new LessonCreationNotification(
                    null,
                    LessonCreationStatusEnum::ERROR->value,
                    __('lessons.lesson_creation_failed')
                ));
                return;
            }
            // Find similar lesson
            $similar_lesson = $this->findSimilarLesson($embedding);
            if ($similar_lesson) {
                $user->notify(new LessonCreationNotification(
                    $similar_lesson,
                    LessonCreationStatusEnum::SUCCESSFULLY->value,
                    __('lessons.lesson_created_successfully', ['name' => $similar_lesson->lesson_name])
                ));
                return;
            }
            // Generate lesson data via OpenAI
            $data = $this->open_ai_service->generateLesson($this->lesson_dto);

            // Create lesson
            $lesson = Lesson::create([
                'user_id' => $this->user_id,
                'topic_id' => $this->topic_id,
                'lesson_name' => $data['lesson_name'] ?? '',
                'lesson_text' => $data['lesson_text'] ?? '',
                'grammar_data' => $data['grammar_highlights'] ?? [],
                'vocabulary' => $data['vocabulary'] ?? [],
                'quiz' => $data['quiz'] ?? [],
                'embedding' => $embedding,
            ]);

            // Notify user of successful creation with link
            $user->notify(new LessonCreationNotification(
                $lesson,
                LessonCreationStatusEnum::SUCCESSFULLY->value,
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
                LessonCreationStatusEnum::ERROR->value,
                __('lessons.lesson_creation_failed')
            ));
        }
    }

    private function findSimilarLesson(array $embedding): ?Lesson
    {
        // Преобразуем в строку вида '[0.123, 0.456, ...]'
        $embedding_str = '[' . implode(',', $embedding) . ']';
        $similarity_threshold = 0.9;

        // Используем оператор косинусного расстояния <=> (работает с pgvector)
        $similar_lesson = Lesson::selectRaw("*, 1 - (embedding <=> '$embedding_str') as similarity")
            ->where('user_id', $this->user_id)
            ->whereNotNull('embedding')
            ->having('similarity', '>=', $similarity_threshold)
            ->orderByDesc('similarity')
            ->first();

        if (!$similar_lesson) {
            return null;
        }

        return $similar_lesson;
    }

}
