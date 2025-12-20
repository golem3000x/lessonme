<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Course;

use App\Enums\Notifications\CourseAssignmentStatusEnum;
use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\User\User;
use App\Notifications\Course\CourseAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAssignedNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    private Course $course;

    private CourseAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);
        $this->student = User::factory()->create();
        $this->course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $this->assignment = CourseAssignment::factory()->create([
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_payload_contains_teacher_name(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('teacher_name', $payload);
        $this->assertEquals($this->teacher->full_name, $payload['teacher_name']);
    }

    public function test_payload_contains_assigned_at(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('assigned_at', $payload);
    }

    public function test_payload_contains_course_details(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('course_id', $payload);
        $this->assertArrayHasKey('course_url', $payload);
        $this->assertArrayHasKey('course_name', $payload);
    }

    public function test_status_is_assigned(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertEquals(CourseAssignmentStatusEnum::ASSIGNED->value, $payload['status']);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $event_name = $notification->broadcastAs();

        $this->assertEquals('course.assignment.assigned', $event_name);
    }

    public function test_broadcast_on_returns_student_private_channel(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $channels = $notification->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals("private-App.Models.User.{$this->student->id}", $channels[0]->name);
    }

    public function test_entity_is_course(): void
    {
        $notification = new CourseAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertEquals('course', $payload['entity']);
    }
}
