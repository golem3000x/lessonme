<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\System\LanguageLevelEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonHistory;
use App\Services\Lesson\LessonHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LessonHistoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonHistoryService();
    }

    public function test_snapshot_creates_history_record(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Original Lesson',
            'lesson_text' => 'Original content',
            'language_level' => LanguageLevelEnum::B1->value,
        ]);

        $history = $this->service->snapshot($lesson);

        $this->assertInstanceOf(LessonHistory::class, $history);
        $this->assertDatabaseHas('lesson_histories', [
            'lesson_id' => $lesson->id,
            'lesson_name' => 'Original Lesson',
            'lesson_text' => 'Original content',
            'language_level' => LanguageLevelEnum::B1->value,
        ]);
    }

    public function test_snapshot_copies_all_fields(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Test Lesson',
            'lesson_text' => 'Test content',
            'language_level' => LanguageLevelEnum::A2->value,
            'grammar_data' => ['rule1' => 'data1'],
            'vocabulary' => ['word1' => 'translation1'],
            'dialogue' => ['line1' => 'Hello'],
            'exercises' => ['ex1' => 'question'],
            'homework' => 'Do homework',
            'common_mistakes' => ['mistake1'],
            'culture_notes' => 'Culture info',
            'embedding' => [],
        ]);

        $history = $this->service->snapshot($lesson);

        $this->assertSame($lesson->id, $history->lesson_id);
        $this->assertSame($lesson->user_id, $history->user_id);
        $this->assertSame($lesson->topic_id, $history->topic_id);
        $this->assertSame('Test Lesson', $history->lesson_name);
        $this->assertSame('Test content', $history->lesson_text);
        $this->assertSame(LanguageLevelEnum::A2, $history->language_level);
        $this->assertSame(['rule1' => 'data1'], $history->grammar_data);
        $this->assertSame(['word1' => 'translation1'], $history->vocabulary);
        $this->assertSame(['line1' => 'Hello'], $history->dialogue);
        $this->assertSame(['ex1' => 'question'], $history->exercises);
        $this->assertSame('Do homework', $history->homework);
        $this->assertSame(['mistake1'], $history->common_mistakes);
        $this->assertSame('Culture info', $history->culture_notes);
        $this->assertSame([], $history->embedding);
    }

    public function test_restore_updates_lesson_with_history_data(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Current Version',
            'lesson_text' => 'Current content',
            'language_level' => LanguageLevelEnum::B2->value,
            'topic_id' => 1, // Ensure topic_id is set
        ]);

        $history = LessonHistory::create([
            'lesson_id' => $lesson->id,
            'user_id' => $lesson->user_id,
            'topic_id' => 1,
            'lesson_name' => 'Old Version',
            'lesson_text' => 'Old content',
            'language_level' => LanguageLevelEnum::A1->value,
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => '',
            'common_mistakes' => [],
            'culture_notes' => '',
            'embedding' => [],
        ]);

        $restored_lesson = $this->service->restore($history);

        $this->assertSame($lesson->id, $restored_lesson->id);
        $this->assertSame('Old Version', $restored_lesson->fresh()->lesson_name);
        $this->assertSame('Old content', $restored_lesson->fresh()->lesson_text);
        $this->assertSame(LanguageLevelEnum::A1, $restored_lesson->fresh()->language_level);
    }

    public function test_restore_creates_snapshot_before_restoring(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Current Version',
            'language_level' => LanguageLevelEnum::C1->value,
            'topic_id' => 1,
        ]);

        $history = LessonHistory::create([
            'lesson_id' => $lesson->id,
            'user_id' => $lesson->user_id,
            'topic_id' => 1,
            'lesson_name' => 'Old Version',
            'lesson_text' => 'Old text',
            'language_level' => LanguageLevelEnum::B1->value,
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => '',
            'common_mistakes' => [],
            'culture_notes' => '',
            'embedding' => [],
        ]);

        $initial_history_count = LessonHistory::count();

        $this->service->restore($history);

        // Should have created one snapshot (and deleted the old history)
        // So count should be the same (created 1, deleted 1)
        $this->assertSame($initial_history_count, LessonHistory::count());
        
        // The new snapshot should have the "Current Version" data
        $latest_history = LessonHistory::where('lesson_id', '=', $lesson->id)->latest()->first();
        $this->assertSame('Current Version', $latest_history->lesson_name);
        $this->assertSame(LanguageLevelEnum::C1, $latest_history->language_level);
    }

    public function test_restore_deletes_history_record(): void
    {
        $lesson = Lesson::factory()->create(['topic_id' => 1]);

        $history = LessonHistory::create([
            'lesson_id' => $lesson->id,
            'user_id' => $lesson->user_id,
            'topic_id' => 1,
            'lesson_name' => 'Old Version',
            'lesson_text' => 'Old text',
            'language_level' => LanguageLevelEnum::A1->value,
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => '',
            'common_mistakes' => [],
            'culture_notes' => '',
            'embedding' => [],
        ]);

        $history_id = $history->id;

        $this->service->restore($history);

        $this->assertDatabaseMissing('lesson_histories', [
            'id' => $history_id,
        ]);
    }

    public function test_restore_returns_updated_lesson(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Current',
            'topic_id' => 1,
        ]);

        $history = LessonHistory::create([
            'lesson_id' => $lesson->id,
            'user_id' => $lesson->user_id,
            'topic_id' => 1,
            'lesson_name' => 'Restored',
            'lesson_text' => 'Restored text',
            'language_level' => LanguageLevelEnum::B1->value,
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => '',
            'common_mistakes' => [],
            'culture_notes' => '',
            'embedding' => [],
        ]);

        $result = $this->service->restore($history);

        $this->assertInstanceOf(Lesson::class, $result);
        $this->assertSame($lesson->id, $result->id);
    }

    public function test_multiple_snapshots_preserve_history(): void
    {
        $lesson = Lesson::factory()->create([
            'lesson_name' => 'Version 1',
            'topic_id' => 1,
        ]);

        // Create first snapshot
        $history1 = $this->service->snapshot($lesson);

        // Update lesson
        $lesson->update(['lesson_name' => 'Version 2']);

        // Create second snapshot
        $history2 = $this->service->snapshot($lesson);

        $this->assertDatabaseHas('lesson_histories', [
            'id' => $history1->id,
            'lesson_name' => 'Version 1',
        ]);

        $this->assertDatabaseHas('lesson_histories', [
            'id' => $history2->id,
            'lesson_name' => 'Version 2',
        ]);

        $this->assertNotSame($history1->id, $history2->id);
    }
}
