<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Enums\LessonCreationStatusEnum;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonService;
use App\Services\Slug\SlugService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SlugService $open_ai_service;

    public function __construct(
        protected int $user_id,
        protected int $topic_id,
        protected LessonDto $lesson_dto,
        protected string $user_locale
    ) {}

    /**
     * @throws Throwable
     * @throws LessonExistsException
     */
    public function handle(LessonService $service): void
    {
        try {
            $lesson = $service->createLesson($this->user_id, $this->topic_id, $this->lesson_dto);
            $this->notify($lesson, LessonCreationStatusEnum::SUCCESSFULLY);
        } catch (LessonExistsException $e) {
            Log::error('Lesson already exists', [
                'user_id' => $this->user_id,
                'topic_id' => $this->topic_id,
                'lesson_id' => $e->lesson->id,
            ]);
            $this->notify($e->lesson, LessonCreationStatusEnum::EXISTS);
        } catch (Throwable $e) {
            Log::error('Lesson creation failed', [
                'user_id' => $this->user_id,
                'topic_id' => $this->topic_id,
                'error' => $e->getMessage(),
            ]);
            $this->notify(null, LessonCreationStatusEnum::ERROR);
            throw $e;
        }
    }

    private function notify(?Lesson $lesson, LessonCreationStatusEnum $status)
    {
        // Set the user's locale for translation
        app()->setLocale($this->user_locale);

        User::find($this->user_id)?->notify(
            new LessonCreationNotification(
                $lesson,
                $status
            )
        );
    }
}
