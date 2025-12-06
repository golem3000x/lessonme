<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonAssignmentRemovedNotification extends AbstractLessonNotification
{
    protected string $student_name;

    public function __construct(User $teacher, Lesson $lesson, string $student_name)
    {
        $this->student_name = $student_name;

        parent::__construct($teacher, $lesson->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return LessonAssignmentStatusEnum::REMOVED;
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

        $base_payload['message'] = __('notifications.lesson_assignment_removed', [
            'student' => $this->student_name,
            'lesson' => $this->entity?->lesson_name ?? '',
        ]);

        return $base_payload;
    }
}
