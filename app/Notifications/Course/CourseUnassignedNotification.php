<?php

declare(strict_types=1);

namespace App\Notifications\Course;

use App\Enums\Notifications\CourseAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Course\Course;
use App\Models\User;
use App\Notifications\Abstracts\AbstractCourseNotification;
use BackedEnum;

class CourseUnassignedNotification extends AbstractCourseNotification
{
    protected string $teacher_name;

    public function __construct(User $student, Course $course, string $teacher_name)
    {
        $this->teacher_name = $teacher_name;

        parent::__construct($student, $course->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return CourseAssignmentStatusEnum::UNASSIGNED;
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

        $base_payload['message'] = __('notifications.course_unassigned', [
            'teacher' => $this->teacher_name,
            'course' => $this->entity?->title ?? '',
        ]);
        $base_payload['teacher_name'] = $this->teacher_name;

        return $base_payload;
    }
}
