<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegenerateLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int       $lesson_id,
        protected LessonDto $lesson_dto
    )
    {
    }

    public function handle(LessonService $lessonService): void
    {
        $lesson = Lesson::find($this->lesson_id);

        if (!$lesson) {
            Log::error('Lesson not found for regeneration', ['lesson_id' => $this->lesson_id]);

            return;
        }

        $user = User::find($lesson->user_id);
        if (!$user) {
            Log::error('User not found for lesson regeneration', ['user_id' => $lesson->user_id]);

            return;
        }

        try {
            $updatedLesson = $lessonService->regenerateLesson($lesson, $this->lesson_dto);
            $this->notify($user, LessonCreationStatusEnum::SUCCESSFULLY,$updatedLesson);
        } catch (LessonExistsException $e) {
            $this->notify($user, LessonCreationStatusEnum::EXISTS, $e->lesson);
        } catch (Exception $e) {
            Log::error('Lesson regeneration failed', [
                'lesson_id' => $this->lesson_id,
                'error' => $e->getMessage(),
            ]);

            $this->notify($user, LessonCreationStatusEnum::ERROR, null);
        }
    }

    private function notify(User $user, LessonCreationStatusEnum $status, ?Lesson $lesson): void
    {
        $user->notify(
            new LessonCreationNotification(
                $user,
                $status,
                $lesson->id,
            )
        );
    }
}
