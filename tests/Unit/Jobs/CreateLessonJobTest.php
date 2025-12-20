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
use App\Models\User\User;
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
        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
        ]);
        $user->syncLearningLanguages([SystemLanguageEnum::VIETNAMESE->value]);

        return $user;
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

        // Create an existing lesson that will be found as similar
        $existing_lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'language_level' => LanguageLevelEnum::B1,
        ]);

        // Create a placeholder lesson that the job will try to fill
        $placeholder_lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'language_level' => LanguageLevelEnum::B1,
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

        $job = new CreateLessonJob($placeholder_lesson->id, $lesson_dto);
        $job->handle($lesson_service, $audio_service);

        Notification::assertSentTo(
            $user,
            LessonCreationNotification::class
        );

        // Placeholder lesson should be deleted, only existing lesson remains
        $this->assertDatabaseCount('lessons', 1);
        $this->assertDatabaseHas('lessons', ['id' => $existing_lesson->id]);
        $this->assertDatabaseMissing('lessons', ['id' => $placeholder_lesson->id]);
    }
}
