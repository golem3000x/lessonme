<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Lesson\LessonAssignment;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonAssignedNotification extends AbstractLessonNotification
{
    protected LessonAssignment $assignment;

    protected string $teacher_name;

    public function __construct(LessonAssignment $assignment)
    {
        $this->assignment = $assignment;

        /** @var User $student */
        $student = $assignment->student;

        $this->teacher_name = $assignment->teacher->full_name;

        parent::__construct($student, $assignment->lesson_id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return LessonAssignmentStatusEnum::ASSIGNED;
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

        $base_payload['message'] = __('notifications.lesson_assigned', [
            'teacher' => $this->teacher_name,
            'lesson' => $this->entity?->lesson_name ?? '',
        ]);
        $base_payload['teacher_name'] = $this->teacher_name;
        $base_payload['assigned_at'] = $this->assignment->assigned_at->toIso8601String();

        return $base_payload;
    }
}
