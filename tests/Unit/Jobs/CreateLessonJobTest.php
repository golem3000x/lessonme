<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Dto\LessonDto;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Lesson\CreateLessonJob;
use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonAudioService;
use App\Services\Lesson\LessonGenerationService;
use App\Services\Lesson\LessonHistoryService;
use App\Services\Lesson\LessonService;
use App\Services\Lesson\LessonSimilarityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class CreateLessonJobTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(): User
    {
        return User::factory()->create([
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
            'learning_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);
    }

    protected function createTopic(): Topic
    {
        $topic = Topic::create([
            'user_id' => null,
            'slug' => 'business-'.uniqid(),
            'is_system' => true,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => SystemLanguageEnum::ENGLISH->value,
            'value' => 'Business',
        ]);

        return $topic;
    }

    protected function createLessonDto(): LessonDto
    {
        return new LessonDto(
            topic: 'Business',
            level: LanguageLevelEnum::B1,
            native_lang: SystemLanguageEnum::ENGLISH->value,
            learning_lang: SystemLanguageEnum::VIETNAMESE->value,
            keywords: ['negotiation', 'meeting']
        );
    }

    public function test_handles_lesson_exists_exception_and_notifies(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $topic = $this->createTopic();
        $lesson_dto = $this->createLessonDto();

        $existing_lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'language_level' => LanguageLevelEnum::B1->value,
        ]);

        // Mock dependencies
        $generator = Mockery::mock(LessonGenerationService::class);
        $similarity = Mockery::mock(LessonSimilarityService::class);
        $history = Mockery::mock(LessonHistoryService::class);

        $embedding = [0.1, 0.2, 0.3];
        $generator->shouldReceive('createEmbedding')->once()->andReturn($embedding);
        $similarity->shouldReceive('findSimilar')->once()->andReturn($existing_lesson);

        $lesson_service = new LessonService($generator, $similarity, $history);

        $audio_service = Mockery::mock(LessonAudioService::class);
        $audio_service->shouldReceive('generateAudioForLesson')->never();

        $job = new CreateLessonJob($user->id, $topic->id, $lesson_dto);
        $job->handle($lesson_service, $audio_service);

        Notification::assertSentTo(
            $user,
            LessonCreationNotification::class
        );

        // Should not create a new lesson
        $this->assertDatabaseCount('lessons', 1);
    }
}
