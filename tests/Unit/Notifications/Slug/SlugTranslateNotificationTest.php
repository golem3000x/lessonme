<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications\Slug;

use App\Enums\Notifications\TranslateStatusEnum;
use App\Models\Keyword\Keyword;
use App\Models\User\User;
use App\Notifications\Slug\Translate\SlugTranslateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugTranslateNotificationTest extends TestCase
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
            'slug' => 'translate-keyword',
            'is_system' => false,
            'is_approved' => true,
        ]);
    }

    public function test_notification_uses_database_and_broadcast_channels(): void
    {
        $notification = new SlugTranslateNotification(
            $this->keyword,
            TranslateStatusEnum::SUCCESSFULLY
        );

        $this->assertEquals(['database', 'broadcast'], $notification->via());
    }

    public function test_successfully_status(): void
    {
        $notification = new SlugTranslateNotification(
            $this->keyword,
            TranslateStatusEnum::SUCCESSFULLY
        );

        $payload = $notification->toArray();

        $this->assertEquals('successfully', $payload['status']);
    }

    public function test_invalid_status(): void
    {
        $notification = new SlugTranslateNotification(
            $this->keyword,
            TranslateStatusEnum::INVALID
        );

        $payload = $notification->toArray();

        $this->assertEquals('invalid', $payload['status']);
    }

    public function test_broadcast_as_returns_correct_event_name(): void
    {
        $notification = new SlugTranslateNotification(
            $this->keyword,
            TranslateStatusEnum::SUCCESSFULLY
        );

        $event_name = $notification->broadcastAs();

        $this->assertEquals('keyword.translate.successfully', $event_name);
    }

    public function test_payload_contains_slug(): void
    {
        $notification = new SlugTranslateNotification(
            $this->keyword,
            TranslateStatusEnum::SUCCESSFULLY
        );

        $payload = $notification->toArray();

        $this->assertArrayHasKey('slug', $payload);
        $this->assertEquals('translate-keyword', $payload['slug']);
    }
}
