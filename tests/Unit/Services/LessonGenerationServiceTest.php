<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\LessonDto;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createLessonDto(LanguageLevelEnum $level = LanguageLevelEnum::B1): LessonDto
    {
        return new LessonDto(
            topic: 'Business Communication',
            level: $level,
            native_lang: SystemLanguageEnum::ENGLISH->value,
            learning_lang: SystemLanguageEnum::VIETNAMESE->value,
            keywords: ['meeting', 'presentation']
        );
    }

    // ===== LESSON DTO TESTS =====

    public function test_lesson_dto_stores_topic(): void
    {
        $dto = $this->createLessonDto();
        $this->assertEquals('Business Communication', $dto->topic);
    }

    public function test_lesson_dto_stores_level(): void
    {
        $dto = $this->createLessonDto(LanguageLevelEnum::A1);
        $this->assertEquals(LanguageLevelEnum::A1, $dto->level);
    }

    public function test_lesson_dto_stores_native_lang(): void
    {
        $dto = $this->createLessonDto();
        $this->assertEquals('en', $dto->native_lang);
    }

    public function test_lesson_dto_stores_learning_lang(): void
    {
        $dto = $this->createLessonDto();
        $this->assertEquals('vi', $dto->learning_lang);
    }

    public function test_lesson_dto_stores_keywords(): void
    {
        $dto = $this->createLessonDto();
        $this->assertEquals(['meeting', 'presentation'], $dto->keywords);
    }

    public function test_lesson_dto_get_keywords_as_string(): void
    {
        $dto = $this->createLessonDto();
        $keywords_string = $dto->getKeywordsAsString();

        $this->assertStringContainsString('meeting', $keywords_string);
        $this->assertStringContainsString('presentation', $keywords_string);
    }

    public function test_lesson_dto_level_value(): void
    {
        $dto = $this->createLessonDto(LanguageLevelEnum::B2);
        $this->assertEquals('B2', $dto->levelValue());
    }

    // ===== LEVEL-BASED TESTS =====

    public function test_all_levels_can_create_dto(): void
    {
        $levels = [
            LanguageLevelEnum::A1,
            LanguageLevelEnum::A2,
            LanguageLevelEnum::B1,
            LanguageLevelEnum::B2,
            LanguageLevelEnum::C1,
            LanguageLevelEnum::C2,
        ];

        foreach ($levels as $level) {
            $dto = new LessonDto(
                topic: 'Test',
                level: $level,
                native_lang: 'en',
                learning_lang: 'vi',
                keywords: []
            );

            $this->assertInstanceOf(LessonDto::class, $dto);
            $this->assertEquals($level, $dto->level);
        }
    }

    public function test_empty_keywords_returns_empty_string(): void
    {
        $dto = new LessonDto(
            topic: 'Test',
            level: LanguageLevelEnum::A1,
            native_lang: 'en',
            learning_lang: 'vi',
            keywords: []
        );

        $this->assertEquals('', $dto->getKeywordsAsString());
    }

    public function test_single_keyword(): void
    {
        $dto = new LessonDto(
            topic: 'Test',
            level: LanguageLevelEnum::A1,
            native_lang: 'en',
            learning_lang: 'vi',
            keywords: ['hello']
        );

        $this->assertEquals('hello', $dto->getKeywordsAsString());
    }
}
