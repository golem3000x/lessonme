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
        protected int $user_id,
        protected int $topic_id,
        protected LessonDto $lesson_dto
    ) {}

    /**
     * @throws Throwable
     * @throws LessonExistsException
     */
    public function handle(LessonService $service, LessonAudioService $audio_service): void
    {
        try {
            $lesson = $service->createLesson($this->user_id, $this->topic_id, $this->lesson_dto);

            try {
                $audio_service->generateAudioForLesson($lesson);
            } catch (\Exception $e) {
                Log::error('Audio generation failed for lesson creation', [
                    'lesson_id' => $lesson->id,
                    'error' => $e->getMessage(),
                ]);
            }

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
            $this->notify(new Lesson, LessonCreationStatusEnum::ERROR);
            throw $e;
        }
    }

    private function notify(Lesson $lesson, LessonCreationStatusEnum $status): void
    {
        $user = User::find($this->user_id);
        if (! $user) {
            Log::warning('User not found for notification', ['user_id' => $this->user_id]);

            return;
        }

        $user->notify(
            new LessonCreationNotification(
                $user,
                $status,
                $lesson->id,
            )
        );
    }
}
