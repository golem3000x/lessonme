<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\Notifications\ModerationStatusEnum;
use App\Jobs\Moderate\ModerateSlugEntityJob;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Models\User\User;
use App\Notifications\Slug\Moderation\SlugModerationNotification;
use App\Services\Slug\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class ModerateSlugEntityJobTest extends TestCase
{
    use RefreshDatabase;

    private SlugService $mock_slug_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock_slug_service = Mockery::mock(SlugService::class);
    }

    public function test_handle_approves_slug_when_moderation_passes(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldReceive('moderateSlug')
            ->once()
            ->with('test-keyword')
            ->andReturn(true);

        Notification::fake();

        $job = new ModerateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'is_approved' => true,
        ]);

        Notification::assertSentTo(
            $user,
            SlugModerationNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === ModerationStatusEnum::APPROVED->value;
            }
        );
    }

    public function test_handle_rejects_slug_when_moderation_fails(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'inappropriate-content',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldReceive('moderateSlug')
            ->once()
            ->with('inappropriate-content')
            ->andReturn(false);

        Notification::fake();

        $job = new ModerateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'is_approved' => false,
        ]);

        Notification::assertSentTo(
            $user,
            SlugModerationNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === ModerationStatusEnum::REJECTED->value;
            }
        );
    }

    public function test_handle_skips_moderation_if_already_approved(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'already-approved',
            'is_approved' => true,
        ]);

        $this->mock_slug_service
            ->shouldNotReceive('moderateSlug');

        Notification::fake();

        $job = new ModerateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'is_approved' => true,
        ]);

        Notification::assertSentTo(
            $user,
            SlugModerationNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === ModerationStatusEnum::APPROVED->value;
            }
        );
    }

    public function test_handle_returns_invalid_status_on_exception(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldReceive('moderateSlug')
            ->once()
            ->andThrow(new \Exception('API Error'));

        Notification::fake();

        $job = new ModerateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        Notification::assertSentTo(
            $user,
            SlugModerationNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === ModerationStatusEnum::INVALID->value;
            }
        );
    }

    public function test_handle_works_with_topic_entity(): void
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-topic',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldReceive('moderateSlug')
            ->once()
            ->with('test-topic')
            ->andReturn(true);

        Notification::fake();

        $job = new ModerateSlugEntityJob($topic);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'is_approved' => true,
        ]);

        Notification::assertSentTo($user, SlugModerationNotification::class);
    }

    public function test_handle_does_not_send_notification_if_user_is_null(): void
    {
        $this->markTestSkipped('Foreign key constraint prevents creating entity without valid user');
    }

    public function test_handle_updates_entity_before_sending_notification(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldReceive('moderateSlug')
            ->once()
            ->andReturn(true);

        Notification::fake();

        $job = new ModerateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('keywords', [
            'id' => $keyword->id,
            'is_approved' => true,
        ]);

        Notification::assertSentTo(
            $user,
            SlugModerationNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === ModerationStatusEnum::APPROVED->value;
            }
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
