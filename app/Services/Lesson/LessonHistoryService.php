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
            'grammar_data' => $lesson->grammar_data,
            'vocabulary' => $lesson->vocabulary,
            'quiz' => $lesson->quiz,
            'embedding' => $lesson->embedding,
        ]);
    }

    public function restore(LessonHistory $history): Lesson
    {
        $lesson = $history->lesson;

        $lesson->update([
            'topic_id' => $history->topic_id,
            'lesson_name' => $history->lesson_name,
            'lesson_text' => $history->lesson_text,
            'language_level' => $history->language_level,
            'grammar_data' => $history->grammar_data,
            'vocabulary' => $history->vocabulary,
            'quiz' => $history->quiz,
            'embedding' => $history->embedding,
        ]);

        return $lesson;
    }
}
