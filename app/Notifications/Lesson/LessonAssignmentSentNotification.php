<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonAssignmentSentNotification extends AbstractLessonNotification
{
    protected int $assigned_count;

    public function __construct(User $teacher, Lesson $lesson, int $assigned_count)
    {
        $this->assigned_count = $assigned_count;

        parent::__construct($teacher, $lesson->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return LessonAssignmentStatusEnum::SENT;
    }

    protected function getEventType(): BackedEnum
    {
        return NotificationEventTypeEnum::ASSIGNMENT;
    }

    /**
     * @return array<string, mixed>
     */
    protected function payload(): array
    {
        $base_payload = parent::payload();

        $base_payload['message'] = __('notifications.lesson_assignment_sent', [
            'count' => $this->assigned_count,
            'lesson' => $this->entity?->lesson_name ?? '',
        ]);
        $base_payload['assigned_count'] = $this->assigned_count;

        return $base_payload;
    }
}
