<?php

declare(strict_types=1);

namespace App\Notifications\Course;

use App\Enums\Notifications\CourseAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Course\CourseAssignment;
use App\Models\User;
use App\Notifications\Abstracts\AbstractCourseNotification;
use BackedEnum;

class CourseAssignedNotification extends AbstractCourseNotification
{
    protected CourseAssignment $assignment;

    protected string $teacher_name;

    public function __construct(CourseAssignment $assignment)
    {
        $this->assignment = $assignment;

        /** @var User $student */
        $student = $assignment->student;

        $this->teacher_name = $assignment->teacher->full_name;

        parent::__construct($student, $assignment->course_id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return CourseAssignmentStatusEnum::ASSIGNED;
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

        $base_payload['message'] = __('notifications.course_assigned', [
            'teacher' => $this->teacher_name,
            'course' => $this->entity?->title ?? '',
        ]);
        $base_payload['teacher_name'] = $this->teacher_name;
        $base_payload['assigned_at'] = $this->assignment->assigned_at->toIso8601String();

        return $base_payload;
    }
}
