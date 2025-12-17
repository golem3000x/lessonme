<?php

declare(strict_types=1);

namespace App\Notifications\Course;

use App\Enums\Notifications\CourseAssignmentStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\Course\Course;
use App\Models\User;
use App\Notifications\Abstracts\AbstractCourseNotification;
use BackedEnum;

class CourseAssignmentSentNotification extends AbstractCourseNotification
{
    protected int $assigned_count;

    public function __construct(User $teacher, Course $course, int $assigned_count)
    {
        $this->assigned_count = $assigned_count;

        parent::__construct($teacher, $course->id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return CourseAssignmentStatusEnum::SENT;
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

        $base_payload['message'] = __('notifications.course_assignment_sent', [
            'count' => $this->assigned_count,
            'course' => $this->entity?->title ?? '',
        ]);
        $base_payload['assigned_count'] = $this->assigned_count;

        return $base_payload;
    }
}
