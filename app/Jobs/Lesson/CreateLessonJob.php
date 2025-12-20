<?php

declare(strict_types=1);

namespace App\Jobs\Lesson;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonAudioService;
use App\Services\Lesson\LessonService;
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

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 180;

    public function __construct(
        protected int $lesson_id,
        protected LessonDto $lesson_dto
    ) {}

    /**
     * @throws Throwable
     * @throws LessonExistsException
     */
    public function handle(LessonService $service, LessonAudioService $audio_service): void
    {
        $lesson = Lesson::find($this->lesson_id);

        if (! $lesson) {
            Log::error('Lesson not found for creation', ['lesson_id' => $this->lesson_id]);

            return;
        }

        $user = User::find($lesson->user_id);
        if (! $user) {
            Log::error('User not found for lesson', ['lesson_id' => $this->lesson_id]);

            return;
        }

        try {
            $lesson = $service->fillLessonContent($lesson, $this->lesson_dto);

            try {
                $audio_service->generateAudioForLesson($lesson);
            } catch (\Exception $e) {
                Log::error('Audio generation failed for lesson creation', [
                    'lesson_id' => $lesson->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Set status to CREATED only after all operations complete (content + audio)
            $lesson->update(['status' => LessonCreationStatusEnum::CREATED]);

            $this->notify($user, LessonCreationStatusEnum::CREATED, $lesson->id);
        } catch (LessonExistsException $e) {
            Log::info('Similar lesson already exists', [
                'lesson_id' => $this->lesson_id,
                'similar_lesson_id' => $e->lesson->id,
            ]);
            $lesson->delete();
            $this->notify($user, LessonCreationStatusEnum::EXISTS, $e->lesson->id);
        } catch (Throwable $e) {
            Log::error('Lesson creation failed', [
                'lesson_id' => $this->lesson_id,
                'error' => $e->getMessage(),
            ]);
            $lesson->delete();
            $this->notify($user, LessonCreationStatusEnum::ERROR, null);
            throw $e;
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
