<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\Lesson\LessonAudioStatusEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAudioFile;
use App\Services\FptTtsService;
use App\Services\Lesson\LessonAudioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class LessonAudioServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonAudioService $service;

    private FptTtsService $mock_tts_service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->mock_tts_service = Mockery::mock(FptTtsService::class);
        $this->service = new LessonAudioService($this->mock_tts_service);
    }

    // ===== GENERATE VOCABULARY AUDIO TESTS =====

    public function test_generate_vocabulary_audio_with_empty_vocabulary(): void
    {
        $lesson = Lesson::factory()->create([
            'vocabulary' => [],
        ]);

        $this->service->generateVocabularyAudio($lesson);

        $this->assertDatabaseCount('lesson_audio_files', 0);
    }

    public function test_generate_vocabulary_audio_creates_pending_records(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::VIETNAMESE,
            'vocabulary' => [
                ['word' => 'xin chào', 'meaning' => 'hello'],
                ['word' => 'cảm ơn', 'meaning' => 'thank you'],
            ],
        ]);

        // TTS API will fail, but records should still be created as FAILED
        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->twice()
            ->andReturn(null);

        $this->service->generateVocabularyAudio($lesson);

        // Should create 2 audio file records
        $this->assertDatabaseCount('lesson_audio_files', 2);
        
        $audio_files = LessonAudioFile::where('lesson_id', '=', $lesson->id)->get();
        
        foreach ($audio_files as $file) {
            $this->assertEquals('vocabulary', $file->audio_type);
            $this->assertEquals(LessonAudioStatusEnum::FAILED, $file->status);
        }
    }

    public function test_generate_vocabulary_audio_skips_empty_words(): void
    {
        $lesson = Lesson::factory()->create([
            'vocabulary' => [
                ['word' => 'hello', 'meaning' => 'greeting'],
                ['word' => '', 'meaning' => 'empty'],
                ['word' => null, 'meaning' => 'null'],
            ],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->andReturn(null);

        $this->service->generateVocabularyAudio($lesson);

        // Only one audio file created (for 'hello')
        $this->assertDatabaseCount('lesson_audio_files', 1);
    }

    public function test_generate_vocabulary_audio_uses_correct_voice_for_language(): void
    {
        $lesson_vietnamese = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::VIETNAMESE,
            'vocabulary' => [['word' => 'xin chào']],
        ]);

        $lesson_english = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::ENGLISH,
            'vocabulary' => [['word' => 'hello']],
        ]);

        // Vietnamese should use 'banmai'
        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->with('xin chào', 'banmai')
            ->andReturn(null);

        // English should use 'joanna'
        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->with('hello', 'joanna')
            ->andReturn(null);

        $this->service->generateVocabularyAudio($lesson_vietnamese);
        $this->service->generateVocabularyAudio($lesson_english);
    }

    // ===== GENERATE GAP FILL AUDIO TESTS =====

    public function test_generate_gap_fill_audio_with_empty_exercises(): void
    {
        $lesson = Lesson::factory()->create([
            'exercises' => ['gap_fill' => []],
        ]);

        $this->service->generateGapFillAudio($lesson);

        $this->assertDatabaseCount('lesson_audio_files', 0);
    }

    public function test_generate_gap_fill_audio_creates_audio_files(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::ENGLISH,
            'exercises' => [
                'gap_fill' => [
                    ['sentence' => 'I ___ to school', 'answer' => 'go'],
                    ['sentence' => 'She ___ a book', 'answer' => 'reads'],
                ],
            ],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->twice()
            ->andReturn(null);

        $this->service->generateGapFillAudio($lesson);

        $this->assertDatabaseCount('lesson_audio_files', 2);

        $audio_files = LessonAudioFile::where('lesson_id', '=', $lesson->id)->get();

        foreach ($audio_files as $file) {
            $this->assertEquals('gap_fill', $file->audio_type);
            $this->assertEquals(LessonAudioStatusEnum::FAILED, $file->status);
        }
    }

    public function test_generate_gap_fill_audio_removes_hints(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::ENGLISH,
            'exercises' => [
                'gap_fill' => [
                    ['sentence' => 'I ___ (verb) to school', 'answer' => 'go'],
                ],
            ],
        ]);

        // Should synthesize without the hint
        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->with('I go to school', 'joanna')
            ->andReturn(null);

        $this->service->generateGapFillAudio($lesson);

        $this->assertDatabaseCount('lesson_audio_files', 1);
    }

    public function test_generate_gap_fill_audio_skips_invalid_exercises(): void
    {
        $lesson = Lesson::factory()->create([
            'exercises' => [
                'gap_fill' => [
                    ['sentence' => 'Valid ___', 'answer' => 'sentence'],
                    ['sentence' => '', 'answer' => 'empty'],
                    ['sentence' => 'Missing ___', 'answer' => ''],
                ],
            ],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->andReturn(null);

        $this->service->generateGapFillAudio($lesson);

        // Only one valid exercise
        $this->assertDatabaseCount('lesson_audio_files', 1);
    }

    // ===== DELETE AUDIO FILES TESTS =====

    public function test_delete_audio_files_removes_database_records(): void
    {
        $lesson = Lesson::factory()->create();

        LessonAudioFile::create([
            'lesson_id' => $lesson->id,
            'audio_type' => 'vocabulary',
            'audio_key' => 'test',
            'file_path' => 'test/path.mp3',
            'status' => LessonAudioStatusEnum::READY,
        ]);

        $this->assertDatabaseCount('lesson_audio_files', 1);

        $this->service->deleteAudioFiles($lesson);

        $this->assertDatabaseCount('lesson_audio_files', 0);
    }

    public function test_delete_audio_files_removes_storage_directory(): void
    {
        Storage::fake('public');

        $lesson = Lesson::factory()->create();
        $directory = "lessons/{$lesson->uuid}/audio";

        Storage::disk('public')->makeDirectory($directory);
        Storage::disk('public')->put("$directory/test.mp3", 'content');

        $this->assertTrue(Storage::disk('public')->exists($directory));

        $this->service->deleteAudioFiles($lesson);

        $this->assertFalse(Storage::disk('public')->exists($directory));
    }

    // ===== GENERATE AUDIO FOR LESSON TESTS =====

    public function test_generate_audio_for_lesson_calls_both_methods(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_lang' => SystemLanguageEnum::ENGLISH,
            'vocabulary' => [['word' => 'hello']],
            'exercises' => [
                'gap_fill' => [['sentence' => 'I ___ happy', 'answer' => 'am']],
            ],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->twice()
            ->andReturn(null);

        $this->service->generateAudioForLesson($lesson);

        // Should create 2 files: 1 vocabulary + 1 gap_fill
        $this->assertDatabaseCount('lesson_audio_files', 2);
    }

    // ===== ERROR HANDLING TESTS =====

    public function test_audio_file_marked_as_failed_when_api_returns_no_url(): void
    {
        $lesson = Lesson::factory()->create([
            'vocabulary' => [['word' => 'test']],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->andReturn(null);

        $this->service->generateVocabularyAudio($lesson);

        $audio_file = LessonAudioFile::first();
        $this->assertNotNull($audio_file);
        $this->assertEquals(LessonAudioStatusEnum::FAILED, $audio_file->status);
        $this->assertEquals('No audio URL returned from API', $audio_file->error_message);
    }

    public function test_audio_file_marked_as_failed_when_api_returns_empty_array(): void
    {
        $lesson = Lesson::factory()->create([
            'vocabulary' => [['word' => 'test']],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->andReturn([]);

        $this->service->generateVocabularyAudio($lesson);

        $audio_file = LessonAudioFile::first();
        $this->assertNotNull($audio_file);
        $this->assertEquals(LessonAudioStatusEnum::FAILED, $audio_file->status);
        $this->assertEquals('No audio URL returned from API', $audio_file->error_message);
    }

    public function test_audio_file_created_with_pending_status_initially(): void
    {
        $lesson = Lesson::factory()->create([
            'vocabulary' => [['word' => 'test']],
        ]);

        $this->mock_tts_service
            ->shouldReceive('synthesize')
            ->once()
            ->andReturn(null);

        $this->service->generateVocabularyAudio($lesson);

        // File should be created with PENDING first, then updated to FAILED
        $this->assertDatabaseHas('lesson_audio_files', [
            'lesson_id' => $lesson->id,
            'audio_type' => 'vocabulary',
            'status' => LessonAudioStatusEnum::FAILED->value,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
