<?php

declare(strict_types=1);

namespace App\Notifications\Course;

use App\Enums\Notifications\CourseAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Course\Course;
use App\Models\User\User;
use App\Notifications\Abstracts\AbstractCourseNotification;
use BackedEnum;

class CourseAssignmentRemovedNotification extends AbstractCourseNotification
{
    protected string $student_name;

    public function __construct(User $teacher, Course $course, string $student_name)
    {
        $this->student_name = $student_name;

        parent::__construct($teacher, $course->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return CourseAssignmentStatusEnum::REMOVED;
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

        $base_payload['message'] = __('notifications.course_assignment_removed', [
            'student' => $this->student_name,
            'course' => $this->entity !== null ? $this->entity->title : '',
        ]);
        $base_payload['student_name'] = $this->student_name;

        return $base_payload;
    }
}
