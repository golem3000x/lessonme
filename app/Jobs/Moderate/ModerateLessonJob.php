<?php

declare(strict_types=1);

namespace App\Jobs\Moderate;

use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Models\Lesson\Lesson;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Slug\SlugService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModerateLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lesson $lesson
    ) {}

    public function handle(SlugService $moderation_service): void
    {
        $lesson = $this->lesson->load('user');

        if (! $lesson->user) {
            Log::error('User not found for lesson moderation', ['lesson_id' => $lesson->id]);

            return;
        }

        try {
            $content_to_moderate = $this->buildContentForModeration($lesson);

            $is_appropriate = $moderation_service->moderateSlug($content_to_moderate);

            if (! $is_appropriate) {
                $lesson->update(['status' => LessonCreationStatusEnum::REJECTED]);

                $lesson->user->notify(new LessonCreationNotification(
                    $lesson->user,
                    LessonCreationStatusEnum::REJECTED,
                    $lesson->id,
                ));

                return;
            }

            $lesson->update(['status' => LessonCreationStatusEnum::CREATED]);

            $lesson->user->notify(new LessonCreationNotification(
                $lesson->user,
                LessonCreationStatusEnum::CREATED,
                $lesson->id,
            ));
        } catch (Exception $e) {
            Log::error('Lesson moderation failed', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
            ]);

            $lesson->update(['status' => LessonCreationStatusEnum::ERROR]);

            $lesson->user->notify(new LessonCreationNotification(
                $lesson->user,
                LessonCreationStatusEnum::ERROR,
                $lesson->id,
            ));
        }
    }

    private function buildContentForModeration(Lesson $lesson): string
    {
        $parts = [];

        $parts[] = $lesson->lesson_name;

        if ($lesson->lesson_text) {
            $parts[] = $lesson->lesson_text;
        }

        if (! empty($lesson->vocabulary)) {
            foreach ($lesson->vocabulary as $item) {
                if (isset($item['word'])) {
                    $parts[] = $item['word'];
                }
                if (isset($item['translation'])) {
                    $parts[] = $item['translation'];
                }
                if (isset($item['example'])) {
                    $parts[] = $item['example'];
                }
            }
        }

        if (! empty($lesson->dialogue)) {
            foreach ($lesson->dialogue as $line) {
                if (isset($line['line'])) {
                    $parts[] = $line['line'];
                }
            }
        }

        if ($lesson->homework) {
            $parts[] = $lesson->homework;
        }

        if ($lesson->culture_notes) {
            $parts[] = $lesson->culture_notes;
        }

        return implode("\n", array_filter($parts));
    }
}
