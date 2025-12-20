<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Slug;

use App\Enums\Notifications\ModerationStatusEnum;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Models\User\User;
use App\Notifications\Slug\Moderation\SlugModerationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugModerationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Keyword $keyword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->keyword = Keyword::create([
            'user_id' => $this->user->id,
            'slug' => 'test-keyword',
            'is_system' => false,
            'is_approved' => false,
        ]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_payload_contains_slug(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $payload = $notification->toArray();

        $this->assertArrayHasKey('slug', $payload);
        $this->assertEquals('test-keyword', $payload['slug']);
    }

    public function test_payload_contains_entity_id(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $payload = $notification->toArray();

        $this->assertArrayHasKey('id', $payload);
        $this->assertEquals($this->keyword->id, $payload['id']);
    }

    public function test_approved_status(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $payload = $notification->toArray();

        $this->assertEquals('approved', $payload['status']);
    }

    public function test_rejected_status(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::REJECTED
        );

        $payload = $notification->toArray();

        $this->assertEquals('rejected', $payload['status']);
    }

    public function test_invalid_status(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::INVALID
        );

        $payload = $notification->toArray();

        $this->assertEquals('invalid', $payload['status']);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $event_name = $notification->broadcastAs();

        $this->assertEquals('keyword.moderation.approved', $event_name);
    }

    public function test_broadcast_on_returns_user_private_channel(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $channels = $notification->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals("private-App.Models.User.{$this->user->id}", $channels[0]->name);
    }

    public function test_entity_is_keyword(): void
    {
        $notification = new SlugModerationNotification(
            $this->keyword,
            ModerationStatusEnum::APPROVED
        );

        $payload = $notification->toArray();

        $this->assertEquals('keyword', $payload['entity']);
    }

    public function test_works_with_topic_entity(): void
    {
        $topic = Topic::create([
            'user_id' => $this->user->id,
            'slug' => 'test-topic',
            'is_system' => false,
            'is_approved' => false,
        ]);

        $notification = new SlugModerationNotification(
            $topic,
            ModerationStatusEnum::APPROVED
        );

        $payload = $notification->toArray();

        $this->assertEquals('topic', $payload['entity']);
        $this->assertEquals('test-topic', $payload['slug']);
    }
}
