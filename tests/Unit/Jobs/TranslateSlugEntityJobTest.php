<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\Notifications\TranslateStatusEnum;
use App\Jobs\Translate\TranslateSlugEntityJob;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Models\User\User;
use App\Notifications\Slug\Translate\SlugTranslateNotification;
use App\Services\Slug\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class TranslateSlugEntityJobTest extends TestCase
{
    use RefreshDatabase;

    private SlugService $mock_slug_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock_slug_service = Mockery::mock(SlugService::class);
    }

    public function test_handle_translates_and_creates_translations_for_approved_keyword(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => true,
        ]);

        $translations = [
            'en' => 'Test Keyword',
            'vi' => 'Từ khóa kiểm tra',
        ];

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->with('test-keyword')
            ->andReturn($translations);

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => 'en',
            'value' => 'Test Keyword',
        ]);

        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => 'vi',
            'value' => 'Từ khóa kiểm tra',
        ]);

        Notification::assertSentTo(
            $user,
            SlugTranslateNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === TranslateStatusEnum::SUCCESSFULLY->value;
            }
        );
    }

    public function test_handle_creates_visibility_record_after_translation(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => true,
        ]);

        $translations = [
            'en' => 'Test',
            'vi' => 'Kiểm tra',
        ];

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andReturn($translations);

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('user_keyword_visibility', [
            'keyword_id' => $keyword->id,
            'user_id' => $user->id,
            'is_hidden' => false,
        ]);
    }

    public function test_handle_returns_invalid_status_for_unapproved_keyword(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'unapproved-keyword',
            'is_approved' => false,
        ]);

        $this->mock_slug_service
            ->shouldNotReceive('translateSlugAllLanguages');

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        Notification::assertSentTo(
            $user,
            SlugTranslateNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === TranslateStatusEnum::INVALID->value;
            }
        );
    }

    public function test_handle_returns_invalid_status_when_translations_are_empty(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => true,
        ]);

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andReturn([]);

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        Notification::assertSentTo(
            $user,
            SlugTranslateNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === TranslateStatusEnum::INVALID->value;
            }
        );
    }

    public function test_handle_returns_invalid_status_on_exception(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => true,
        ]);

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andThrow(new \Exception('Translation API Error'));

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        Notification::assertSentTo(
            $user,
            SlugTranslateNotification::class,
            function ($notification) use ($user) {
                $data = $notification->toArray($user);

                return $data['status'] === TranslateStatusEnum::INVALID->value;
            }
        );
    }

    public function test_handle_works_with_topic_entity(): void
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-topic',
            'is_approved' => true,
        ]);

        $translations = [
            'en' => 'Test Topic',
            'vi' => 'Chủ đề kiểm tra',
        ];

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andReturn($translations);

        Notification::fake();

        $job = new TranslateSlugEntityJob($topic);
        $job->handle($this->mock_slug_service);

        $this->assertDatabaseHas('topic_translations', [
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => 'Test Topic',
        ]);

        $this->assertDatabaseHas('user_topic_visibility', [
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'is_hidden' => false,
        ]);

        Notification::assertSentTo($user, SlugTranslateNotification::class);
    }

    public function test_handle_upserts_existing_translations(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-keyword',
            'is_approved' => true,
        ]);

        // Create existing translation
        $keyword->translations()->create([
            'language_code' => 'en',
            'value' => 'Old Translation',
        ]);

        $new_translations = [
            'en' => 'New Translation',
            'vi' => 'Bản dịch mới',
        ];

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andReturn($new_translations);

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        // Verify old translation was updated
        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => 'en',
            'value' => 'New Translation',
        ]);

        // Verify new translation was created
        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => 'vi',
            'value' => 'Bản dịch mới',
        ]);

        // Should have exactly 2 translations
        $this->assertEquals(2, $keyword->translations()->count());
    }

    public function test_handle_does_not_send_notification_if_user_is_null(): void
    {
        $this->markTestSkipped('Foreign key constraint prevents creating entity without valid user');
    }

    public function test_handle_processes_multiple_language_translations(): void
    {
        $user = User::factory()->create();
        $keyword = Keyword::factory()->create([
            'user_id' => $user->id,
            'slug' => 'multilang-keyword',
            'is_approved' => true,
        ]);

        $translations = [
            'en' => 'English',
            'vi' => 'Tiếng Việt',
        ];

        $this->mock_slug_service
            ->shouldReceive('translateSlugAllLanguages')
            ->once()
            ->andReturn($translations);

        Notification::fake();

        $job = new TranslateSlugEntityJob($keyword);
        $job->handle($this->mock_slug_service);

        foreach ($translations as $lang_code => $value) {
            $this->assertDatabaseHas('keyword_translations', [
                'keyword_id' => $keyword->id,
                'language_code' => $lang_code,
                'value' => $value,
            ]);
        }

        $this->assertEquals(2, $keyword->translations()->count());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
