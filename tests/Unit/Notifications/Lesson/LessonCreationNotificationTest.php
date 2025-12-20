<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Lesson;

use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\User\User;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Tests\TestCase;

class LessonCreationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->lesson = Lesson::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_to_array_returns_correct_payload_structure(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $payload = $notification->toArray();

        $this->assertArrayHasKey('id', $payload);
        $this->assertArrayHasKey('status', $payload);
        $this->assertArrayHasKey('entity', $payload);
        $this->assertArrayHasKey('message', $payload);
        $this->assertArrayHasKey('lesson_id', $payload);
        $this->assertArrayHasKey('lesson_url', $payload);
        $this->assertArrayHasKey('lesson_name', $payload);
        $this->assertEquals('created', $payload['status']);
        $this->assertEquals('lesson', $payload['entity']);
    }

    public function test_to_broadcast_returns_broadcast_message(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $broadcast = $notification->toBroadcast();

        $this->assertInstanceOf(BroadcastMessage::class, $broadcast);
    }

    public function test_broadcast_on_returns_private_channel_for_user(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $channels = $notification->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals("private-App.Models.User.{$this->user->id}", $channels[0]->name);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $event_name = $notification->broadcastAs();

        $this->assertEquals('lesson.creation.created', $event_name);
    }

    public function test_notification_handles_null_entity_id(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::ERROR,
            null
        );

        $payload = $notification->toArray();

        $this->assertArrayHasKey('status', $payload);
        $this->assertEquals('error', $payload['status']);
        $this->assertArrayNotHasKey('lesson_id', $payload);
    }

    public function test_notification_with_exists_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::EXISTS,
            $this->lesson->id
        );

        $payload = $notification->toArray();

        $this->assertEquals('exists', $payload['status']);
    }

    public function test_notification_with_regeneration_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::REGENERATION,
            $this->lesson->id
        );

        $payload = $notification->toArray();

        $this->assertEquals('regeneration', $payload['status']);
    }
}
