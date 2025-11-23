<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Exceptions\LessonExistsException;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Notifications\Lesson\LessonCreationNotification;
use App\Services\Lesson\LessonGenerationService;
use App\Services\Lesson\LessonHistoryService;
use App\Services\Lesson\LessonService;
use App\Services\Lesson\LessonSimilarityService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class RegenerateLessonJobTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(): User
    {
        return User::factory()->create([
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
            'learning_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);
    }

    protected function createLessonDto(): LessonDto
    {
        return new LessonDto(
            topic: 'Business',
            level: LanguageLevelEnum::B2,
            native_lang: SystemLanguageEnum::ENGLISH->value,
            learning_lang: SystemLanguageEnum::VIETNAMESE->value,
            keywords: ['presentation', 'report']
        );
    }

    public function test_handles_lesson_not_found(): void
    {
        Notification::fake();

        $lesson_dto = $this->createLessonDto();
        
        // Create service with mocked dependencies
        $generator = Mockery::mock(LessonGenerationService::class);
        $similarity = Mockery::mock(LessonSimilarityService::class);
        $history = Mockery::mock(LessonHistoryService::class);
        $lesson_service = new LessonService($generator, $similarity, $history);

        $job = new RegenerateLessonJob(999999, $lesson_dto);
        $job->handle($lesson_service);

        Notification::assertNothingSent();
    }

    public function test_handles_lesson_exists_exception(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $existing_lesson = Lesson::factory()->create(['user_id' => $user->id]);

        $lesson_dto = $this->createLessonDto();

        // Mock dependencies
        $generator = Mockery::mock(LessonGenerationService::class);
        $similarity = Mockery::mock(LessonSimilarityService::class);
        $history = Mockery::mock(LessonHistoryService::class);

        $embedding = [0.1, 0.2, 0.3];
        $history->shouldReceive('snapshot')->once();
        $generator->shouldReceive('createEmbedding')->once()->andReturn($embedding);
        $similarity->shouldReceive('findSimilar')->once()->andReturn($existing_lesson);

        $lesson_service = new LessonService($generator, $similarity, $history);

        $job = new RegenerateLessonJob($lesson->id, $lesson_dto);
        $job->handle($lesson_service);

        Notification::assertSentTo(
            $user,
            LessonCreationNotification::class
        );
    }

}
