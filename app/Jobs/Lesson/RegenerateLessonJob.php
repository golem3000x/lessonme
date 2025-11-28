<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonAudioService;
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
        protected int $lesson_id,
        protected LessonDto $lesson_dto
    ) {}

    public function handle(LessonService $lessonService, LessonAudioService $audio_service): void
    {
        $lesson = Lesson::find($this->lesson_id);

        if (! $lesson) {
            Log::error('Lesson not found for regeneration', ['lesson_id' => $this->lesson_id]);

            return;
        }

        $user = User::find($lesson->user_id);
        if (! $user) {
            Log::error('User not found for lesson regeneration', ['user_id' => $lesson->user_id]);

            return;
        }

        // Security check: Ensure lesson belongs to the user
        // This is a safety measure in case the job is dispatched incorrectly
        if ($lesson->user_id !== $user->id) {
            Log::error('Security: Lesson does not belong to user', [
                'lesson_id' => $lesson->id,
                'lesson_user_id' => $lesson->user_id,
                'expected_user_id' => $user->id,
            ]);

            return;
        }

        try {
            try {
                $audio_service->deleteAudioFiles($lesson);
            } catch (\Exception $e) {
                Log::error('Failed to delete old audio files', [
                    'lesson_id' => $lesson->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Feedbacks are automatically archived to history in regenerateLesson -> snapshot()
            $updatedLesson = $lessonService->regenerateLesson($lesson, $this->lesson_dto);

            try {
                $audio_service->generateAudioForLesson($updatedLesson);
            } catch (\Exception $e) {
                Log::error('Audio generation failed for lesson regenerated', [
                    'lesson_id' => $updatedLesson->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->notify($user, LessonCreationStatusEnum::SUCCESSFULLY, $updatedLesson->id);
        } catch (LessonExistsException $e) {
            $this->notify($user, LessonCreationStatusEnum::EXISTS, $e->lesson->id);
        } catch (Exception $e) {
            Log::error('Lesson regeneration failed', [
                'lesson_id' => $this->lesson_id,
                'error' => $e->getMessage(),
            ]);

            $this->notify($user, LessonCreationStatusEnum::ERROR, null);
        }
    }

    private function notify(User $user, LessonCreationStatusEnum $status, ?int $lesson_id): void
    {
        $user->notify(
            new LessonCreationNotification(
                $user,
                $status,
                $lesson_id,
            )
        );
    }
}
