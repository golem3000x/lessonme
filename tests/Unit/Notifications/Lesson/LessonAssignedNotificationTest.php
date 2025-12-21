<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Lesson;

use App\Enums\Notifications\LessonAssignmentStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\User\User;
use App\Notifications\Lesson\LessonAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonAssignedNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    private Lesson $lesson;

    private LessonAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['name' => 'John Doe']);
        $this->student = User::factory()->create();
        $this->lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $this->assignment = LessonAssignment::factory()->create([
            'lesson_id' => $this->lesson->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_payload_contains_teacher_name(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('teacher_name', $payload);
        $this->assertEquals($this->teacher->name, $payload['teacher_name']);
    }

    public function test_payload_contains_assigned_at(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('assigned_at', $payload);
    }

    public function test_payload_contains_lesson_details(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('lesson_id', $payload);
        $this->assertArrayHasKey('lesson_url', $payload);
        $this->assertArrayHasKey('lesson_name', $payload);
    }

    public function test_status_is_assigned(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $payload = $notification->toArray();

        $this->assertEquals(LessonAssignmentStatusEnum::ASSIGNED->value, $payload['status']);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $event_name = $notification->broadcastAs();

        $this->assertEquals('lesson.assignment.assigned', $event_name);
    }

    public function test_broadcast_on_returns_student_private_channel(): void
    {
        $notification = new LessonAssignedNotification($this->assignment);

        $channels = $notification->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals("private-App.Models.User.{$this->student->id}", $channels[0]->name);
    }
}
