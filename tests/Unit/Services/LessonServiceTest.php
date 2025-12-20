<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\LessonDto;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Services\Lesson\LessonGenerationService;
use App\Services\Lesson\LessonHistoryService;
use App\Services\Lesson\LessonService;
use App\Services\Lesson\LessonSimilarityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LessonServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonService $service;

    private LessonGenerationService $mock_generator;

    private LessonSimilarityService $mock_similarity;

    private LessonHistoryService $mock_history;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock_generator = Mockery::mock(LessonGenerationService::class);
        $this->mock_similarity = Mockery::mock(LessonSimilarityService::class);
        $this->mock_history = Mockery::mock(LessonHistoryService::class);

        $this->service = new LessonService(
            $this->mock_generator,
            $this->mock_similarity,
            $this->mock_history
        );
    }

    private function createLessonDto(): LessonDto
    {
        return new LessonDto(
            topic: 'Business Communication',
            level: LanguageLevelEnum::B1,
            native_lang: SystemLanguageEnum::ENGLISH->value,
            learning_lang: SystemLanguageEnum::VIETNAMESE->value,
            keywords: ['meeting', 'presentation']
        );
    }

    private function createEmbedding(float $base = 0.5): array
    {
        // Create 1536-dimensional embedding as required by pgvector
        $embedding = [];
        for ($i = 0; $i < 1536; $i++) {
            $embedding[] = $base + (random_int(-100, 100) / 10000);
        }

        return $embedding;
    }

    private function mockGeneratedLessonData(): array
    {
        return [
            'lesson_name' => 'Business Communication Lesson',
            'lesson_text' => 'This is a lesson about business communication.',
            'grammar_highlights' => [
                ['pattern' => 'Present Simple', 'explanation' => 'Used for facts'],
            ],
            'vocabulary' => [
                ['word' => 'meeting', 'meaning' => 'cuộc họp'],
                ['word' => 'presentation', 'meaning' => 'bài thuyết trình'],
            ],
            'dialogue' => [
                ['speaker' => 'A', 'text' => 'Good morning!'],
                ['speaker' => 'B', 'text' => 'Hello!'],
            ],
            'exercises' => [
                'gap_fill' => [
                    ['sentence' => 'I ___ to work', 'answer' => 'go'],
                ],
                'quiz' => [
                    ['question' => 'What is meeting?', 'options' => ['A', 'B'], 'answer' => 0],
                ],
            ],
            'homework' => 'Practice vocabulary',
            'common_mistakes' => [
                ['mistake' => 'Wrong word order', 'correction' => 'Correct order'],
            ],
            'culture_notes' => 'Business culture in Vietnam',
        ];
    }

    // ===== FILL LESSON CONTENT TESTS =====

    public function test_fill_lesson_content_successfully_fills_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $embedding = $this->createEmbedding(0.5);
        $generated_data = $this->mockGeneratedLessonData();

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->with($dto)
            ->andReturn($embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with($user->id, $embedding, Mockery::type(LessonDto::class))
            ->andReturn(null);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->with($dto)
            ->andReturn($generated_data);

        $result = $this->service->fillLessonContent($lesson, $dto);

        $this->assertInstanceOf(Lesson::class, $result);
        $this->assertEquals($lesson->id, $result->id);
        $this->assertEquals('Business Communication Lesson', $result->lesson_name);
        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'lesson_name' => 'Business Communication Lesson',
        ]);
    }

    public function test_fill_lesson_content_throws_exception_when_similar_lesson_exists(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $existing_lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $embedding = $this->createEmbedding(0.5);

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->with($dto)
            ->andReturn($embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with($user->id, $embedding, Mockery::type(LessonDto::class))
            ->andReturn($existing_lesson);

        $this->mock_generator
            ->shouldNotReceive('generateLesson');

        $this->expectException(LessonExistsException::class);

        $this->service->fillLessonContent($lesson, $dto);
    }

    public function test_fill_lesson_content_stores_all_generated_data_fields(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $embedding = $this->createEmbedding(0.5);
        $generated_data = $this->mockGeneratedLessonData();

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->andReturn($embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with(Mockery::any(), Mockery::any(), Mockery::type(LessonDto::class))
            ->andReturn(null);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->andReturn($generated_data);

        $result = $this->service->fillLessonContent($lesson, $dto);

        $this->assertNotEmpty($result->grammar_data);
        $this->assertNotEmpty($result->vocabulary);
        $this->assertNotEmpty($result->dialogue);
        $this->assertNotEmpty($result->exercises);
        $this->assertNotEmpty($result->homework);
        $this->assertNotEmpty($result->common_mistakes);
        $this->assertNotEmpty($result->culture_notes);
        $this->assertEquals(2, count($result->vocabulary));
        $this->assertEquals(2, count($result->dialogue));
    }

    public function test_fill_lesson_content_allows_same_lesson_as_similar(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $embedding = $this->createEmbedding(0.5);
        $generated_data = $this->mockGeneratedLessonData();

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->andReturn($embedding);

        // Similarity service returns the same lesson
        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with(Mockery::any(), Mockery::any(), Mockery::type(LessonDto::class))
            ->andReturn($lesson);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->andReturn($generated_data);

        // Should NOT throw exception
        $result = $this->service->fillLessonContent($lesson, $dto);

        $this->assertEquals($lesson->id, $result->id);
    }

    // ===== REGENERATE LESSON TESTS =====

    public function test_regenerate_lesson_successfully_updates_existing_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'lesson_name' => 'Old Lesson',
        ]);

        $dto = $this->createLessonDto();
        $new_embedding = $this->createEmbedding(0.6);
        $new_data = $this->mockGeneratedLessonData();

        $this->mock_history
            ->shouldReceive('snapshot')
            ->once()
            ->with($lesson);

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->with($dto)
            ->andReturn($new_embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with($user->id, $new_embedding, Mockery::type(LessonDto::class))
            ->andReturn(null);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->with($dto)
            ->andReturn($new_data);

        $updated_lesson = $this->service->regenerateLesson($lesson, $dto);

        $this->assertEquals($lesson->id, $updated_lesson->id);
        $this->assertEquals('Business Communication Lesson', $updated_lesson->lesson_name);
        $this->assertIsArray($updated_lesson->embedding);
        $this->assertCount(1536, $updated_lesson->embedding);
        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'lesson_name' => 'Business Communication Lesson',
        ]);
    }

    public function test_regenerate_lesson_creates_history_snapshot(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $new_embedding = $this->createEmbedding(0.6);

        $this->mock_history
            ->shouldReceive('snapshot')
            ->once()
            ->with(Mockery::on(function ($arg) use ($lesson) {
                return $arg->id === $lesson->id;
            }));

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->andReturn($new_embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with(Mockery::any(), Mockery::any(), Mockery::type(LessonDto::class))
            ->andReturn(null);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->andReturn($this->mockGeneratedLessonData());

        $updated_lesson = $this->service->regenerateLesson($lesson, $dto);

        // Verify snapshot was called
        $this->mock_history->shouldHaveReceived('snapshot')->once();

        // Verify lesson was updated
        $this->assertNotNull($updated_lesson);
        $this->assertEquals($lesson->id, $updated_lesson->id);
    }

    public function test_regenerate_lesson_throws_exception_when_similar_lesson_exists(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $other_lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $new_embedding = $this->createEmbedding(0.6);

        $this->mock_history
            ->shouldReceive('snapshot')
            ->once()
            ->with($lesson);

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->andReturn($new_embedding);

        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with($user->id, $new_embedding, Mockery::type(LessonDto::class))
            ->andReturn($other_lesson);

        $this->mock_generator
            ->shouldNotReceive('generateLesson');

        $this->expectException(LessonExistsException::class);

        $this->service->regenerateLesson($lesson, $dto);
    }

    public function test_regenerate_lesson_allows_same_lesson_as_similar(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);
        $dto = $this->createLessonDto();
        $new_embedding = $this->createEmbedding(0.6);

        $this->mock_history
            ->shouldReceive('snapshot')
            ->once();

        $this->mock_generator
            ->shouldReceive('createEmbedding')
            ->once()
            ->andReturn($new_embedding);

        // Similarity service returns the same lesson
        $this->mock_similarity
            ->shouldReceive('findSimilar')
            ->once()
            ->with($user->id, $new_embedding, Mockery::type(LessonDto::class))
            ->andReturn($lesson);

        $this->mock_generator
            ->shouldReceive('generateLesson')
            ->once()
            ->andReturn($this->mockGeneratedLessonData());

        // Should NOT throw exception
        $updated_lesson = $this->service->regenerateLesson($lesson, $dto);

        $this->assertEquals($lesson->id, $updated_lesson->id);
    }

    public function test_regenerate_lesson_updates_all_fields(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'lesson_name' => 'Old Name',
            'lesson_text' => 'Old Text',
            'language_level' => LanguageLevelEnum::A1,
        ]);

        $dto = $this->createLessonDto();
        $new_embedding = $this->createEmbedding(0.6);
        $new_data = $this->mockGeneratedLessonData();

        $this->mock_history->shouldReceive('snapshot')->once();
        $this->mock_generator->shouldReceive('createEmbedding')->once()->andReturn($new_embedding);
        $this->mock_similarity->shouldReceive('findSimilar')->once()->andReturn(null);
        $this->mock_generator->shouldReceive('generateLesson')->once()->andReturn($new_data);

        $updated = $this->service->regenerateLesson($lesson, $dto);

        $this->assertEquals('Business Communication Lesson', $updated->lesson_name);
        $this->assertEquals(LanguageLevelEnum::B1, $updated->language_level);
        $this->assertEquals(SystemLanguageEnum::VIETNAMESE, $updated->lesson_lang);
        $this->assertNotEquals('Old Name', $updated->lesson_name);
        $this->assertNotEquals('Old Text', $updated->lesson_text);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
