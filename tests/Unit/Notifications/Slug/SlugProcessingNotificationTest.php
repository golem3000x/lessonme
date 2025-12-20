<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Slug;

use App\Enums\Notifications\ModerationStatusEnum;
use App\Models\Keyword\Keyword;
use App\Models\User\User;
use App\Notifications\Slug\Processing\SlugProcessingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugProcessingNotificationTest extends TestCase
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
            'slug' => 'processing-keyword',
            'is_system' => false,
            'is_approved' => false,
        ]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new SlugProcessingNotification($this->keyword);

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_status_is_pending(): void
    {
        $notification = new SlugProcessingNotification($this->keyword);

        $payload = $notification->toArray();

        $this->assertEquals(ModerationStatusEnum::PENDING->value, $payload['status']);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new SlugProcessingNotification($this->keyword);

        $event_name = $notification->broadcastAs();

        $this->assertEquals('keyword.creation.pending', $event_name);
    }

    public function test_payload_contains_slug(): void
    {
        $notification = new SlugProcessingNotification($this->keyword);

        $payload = $notification->toArray();

        $this->assertArrayHasKey('slug', $payload);
        $this->assertEquals('processing-keyword', $payload['slug']);
    }
}
