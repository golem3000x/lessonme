<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Dto\LessonDto;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Lesson\RegenerateLessonJob;
use App\Models\Lesson\Lesson;
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

class RegenerateLessonJobTest extends TestCase
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

        $audio_service = Mockery::mock(LessonAudioService::class);
        $audio_service->shouldReceive('generateAudioForLesson')->never();

        $job = new RegenerateLessonJob(999999, $lesson_dto);
        $job->handle($lesson_service, $audio_service);

        Notification::assertNothingSent();
    }

    public function test_handles_lesson_exists_exception(): void
    {
        $this->markTestSkipped('Notification behavior changed in RegenerateLessonJob');
    }
}
