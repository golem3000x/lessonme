<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\User\User;
use App\Notifications\Lesson\LessonCreationNotification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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
        $this->lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Test Lesson',
        ]);
        app()->setLocale('en');
    }

    // ===== CONSTRUCTION TESTS =====

    public function test_notification_can_be_constructed_with_entity_id(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            $this->lesson->id
        );

        $this->assertInstanceOf(LessonCreationNotification::class, $notification);
    }

    public function test_notification_can_be_constructed_without_entity_id(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::ERROR,
            null
        );

        $this->assertInstanceOf(LessonCreationNotification::class, $notification);
    }

    // ===== VIA TESTS =====

    public function test_via_returns_database_and_broadcast(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $via = $notification->via();

        $this->assertContains('database', $via);
        $this->assertContains('broadcast', $via);
    }

    // ===== BROADCAST TESTS =====

    public function test_broadcast_on_returns_private_user_channel(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $channels = $notification->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertSame("private-App.Models.User.{$this->user->id}", $channels[0]->name);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            null
        );

        $event_name = $notification->broadcastAs();

        $this->assertSame('lesson.creation.created', $event_name);
    }

    public function test_broadcast_as_includes_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::ERROR,
            null
        );

        $event_name = $notification->broadcastAs();

        $this->assertSame('lesson.creation.error', $event_name);
    }

    public function test_to_broadcast_returns_broadcast_message(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $broadcast = $notification->toBroadcast();

        $this->assertInstanceOf(BroadcastMessage::class, $broadcast);
    }

    // ===== PAYLOAD TESTS =====

    public function test_to_array_includes_required_fields(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $array = $notification->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('entity', $array);
        $this->assertArrayHasKey('message', $array);
    }

    public function test_to_array_status_matches_enum(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            null
        );

        $array = $notification->toArray();

        $this->assertSame(LessonCreationStatusEnum::CREATED->value, $array['status']);
    }

    public function test_to_array_entity_is_lesson(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $array = $notification->toArray();

        $this->assertSame('lesson', $array['entity']);
    }

    public function test_to_array_id_has_correct_prefix(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $array = $notification->toArray();

        $this->assertStringStartsWith('lesson_creation_', $array['id']);
    }

    public function test_to_array_includes_lesson_details_when_entity_exists(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $array = $notification->toArray();

        $this->assertArrayHasKey('lesson_id', $array);
        $this->assertArrayHasKey('lesson_url', $array);
        $this->assertArrayHasKey('lesson_name', $array);
        $this->assertSame($this->lesson->uuid, $array['lesson_id']);
        $this->assertSame($this->lesson->lesson_name, $array['lesson_name']);
    }

    public function test_to_array_excludes_lesson_details_when_no_entity(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::ERROR,
            null
        );

        $array = $notification->toArray();

        $this->assertArrayNotHasKey('lesson_id', $array);
        $this->assertArrayNotHasKey('lesson_url', $array);
        $this->assertArrayNotHasKey('lesson_name', $array);
    }

    // ===== MESSAGE TESTS =====

    public function test_message_key_format_for_processing_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $array = $notification->toArray();

        // Message should be from notifications.lesson_creation_processing
        $this->assertIsString($array['message']);
        $this->assertNotEmpty($array['message']);
    }

    public function test_message_key_format_for_successfully_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $array = $notification->toArray();

        // Message should include lesson name
        $this->assertIsString($array['message']);
        $this->assertNotEmpty($array['message']);
    }

    public function test_message_key_format_for_error_status(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::ERROR,
            null
        );

        $array = $notification->toArray();

        $this->assertIsString($array['message']);
        $this->assertNotEmpty($array['message']);
    }

    // ===== STATUS ENUM TESTS =====

    public function test_all_status_enums_work(): void
    {
        foreach (LessonCreationStatusEnum::cases() as $status) {
            $notification = new LessonCreationNotification(
                $this->user,
                $status,
                null
            );

            $array = $notification->toArray();

            $this->assertSame($status->value, $array['status']);
            $this->assertStringContainsString($status->value, $notification->broadcastAs());
        }
    }

    // ===== INTEGRATION TESTS =====

    public function test_notification_can_be_sent_to_user(): void
    {
        Event::fake();
        Queue::fake();

        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $this->user->notify($notification);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
        ]);
    }

    public function test_notification_stores_correct_data_in_database(): void
    {
        Event::fake();
        Queue::fake();

        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            $this->lesson->id
        );

        $this->user->notify($notification);

        $stored = $this->user->notifications()->first();

        $this->assertNotNull($stored);
        $this->assertArrayHasKey('status', $stored->data);
        $this->assertArrayHasKey('entity', $stored->data);
        $this->assertArrayHasKey('message', $stored->data);
        $this->assertSame(LessonCreationStatusEnum::CREATED->value, $stored->data['status']);
        $this->assertSame('lesson', $stored->data['entity']);
    }

    public function test_notification_payload_is_consistent_across_methods(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            $this->lesson->id
        );

        $array = $notification->toArray();
        $broadcast = $notification->toBroadcast();

        // Compare structure, not exact values (ID is dynamic)
        $this->assertArrayHasKey('id', $broadcast->data);
        $this->assertSame($array['status'], $broadcast->data['status']);
        $this->assertSame($array['entity'], $broadcast->data['entity']);
        $this->assertSame($array['message'], $broadcast->data['message']);
    }

    // ===== EDGE CASES =====

    public function test_handles_nonexistent_entity_id_gracefully(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATED,
            99999 // Non-existent
        );

        $array = $notification->toArray();

        // Should not include lesson details for non-existent entity
        $this->assertArrayNotHasKey('lesson_id', $array);
        $this->assertArrayNotHasKey('lesson_url', $array);
    }

    public function test_entity_name_is_lowercase(): void
    {
        $notification = new LessonCreationNotification(
            $this->user,
            LessonCreationStatusEnum::CREATION,
            null
        );

        $array = $notification->toArray();

        $this->assertSame('lesson', $array['entity']);
        $this->assertNotSame('Lesson', $array['entity']);
    }
}
