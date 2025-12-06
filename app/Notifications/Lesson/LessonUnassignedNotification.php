<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonUnassignedNotification extends AbstractLessonNotification
{
    protected string $teacher_name;

    public function __construct(User $student, Lesson $lesson, string $teacher_name)
    {
        $this->teacher_name = $teacher_name;

        parent::__construct($student, $lesson->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return LessonAssignmentStatusEnum::UNASSIGNED;
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

        $base_payload['message'] = __('notifications.lesson_unassigned', [
            'teacher' => $this->teacher_name,
            'lesson' => $this->entity?->lesson_name ?? '',
        ]);

        return $base_payload;
    }
}
