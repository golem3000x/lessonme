<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonHistory;

class LessonHistoryService
{
    public function snapshot(Lesson $lesson): LessonHistory
    {
        return LessonHistory::create([
            'lesson_id' => $lesson->id,
            'user_id' => $lesson->user_id,
            'topic_id' => $lesson->topic_id,
            'lesson_name' => $lesson->lesson_name,
            'lesson_text' => $lesson->lesson_text,
            'language_level' => $lesson->language_level,
            'lesson_lang' => $lesson->lesson_lang,
            'grammar_data' => $lesson->grammar_data,
            'vocabulary' => $lesson->vocabulary,
            'dialogue' => $lesson->dialogue,
            'exercises' => $lesson->exercises,
            'homework' => $lesson->homework,
            'common_mistakes' => $lesson->common_mistakes,
            'culture_notes' => $lesson->culture_notes,
            'embedding' => $lesson->embedding,
        ]);
    }

    public function restore(LessonHistory $history): Lesson
    {
        $lesson = $history->lesson;

        $this->snapshot($lesson);

        $lesson->update([
            'uuid' => $history->uuid,
            'topic_id' => $history->topic_id,
            'lesson_name' => $history->lesson_name,
            'lesson_text' => $history->lesson_text,
            'language_level' => $history->language_level,
            'lesson_lang' => $history->lesson_lang,
            'grammar_data' => $history->grammar_data,
            'vocabulary' => $history->vocabulary,
            'dialogue' => $history->dialogue,
            'exercises' => $history->exercises,
            'homework' => $history->homework,
            'common_mistakes' => $history->common_mistakes,
            'culture_notes' => $history->culture_notes,
            'embedding' => $history->embedding,
        ]);

        $history->delete();

        return $lesson;
    }
}
