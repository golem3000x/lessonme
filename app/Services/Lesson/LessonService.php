<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;

class LessonService
{
    public function __construct(
        private LessonGenerationService $generator,
        private LessonSimilarityService $similarity,
        private LessonHistoryService $history,
    ) {}

    /**
     * @throws LessonExistsException
     */
    public function createLesson(int $user_id, int $topic_id, LessonDto $dto): Lesson
    {
        $embedding = $this->generator->createEmbedding($dto);

        $similar_lesson = $this->similarity->findSimilar($user_id, $embedding);
        if ($similar_lesson) {
            throw new LessonExistsException($similar_lesson);
        }

        $data = $this->generator->generateLesson($dto);

        return Lesson::create([
            'user_id' => $user_id,
            'topic_id' => $topic_id,
            'lesson_name' => $data['lesson_name'],
            'lesson_text' => $data['lesson_text'],
            'language_level' => $dto->level,
            'lesson_lang' => $dto->learning_lang,
            'grammar_data' => $data['grammar_highlights'],
            'vocabulary' => $data['vocabulary'],
            'dialogue' => $data['dialogue'] ?? [],
            'exercises' => $data['exercises'] ?? [],
            'homework' => $data['homework'] ?? null,
            'common_mistakes' => $data['common_mistakes'] ?? [],
            'culture_notes' => $data['culture_notes'] ?? null,
            'embedding' => $embedding,
        ]);
    }

    /**
     * @throws LessonExistsException
     */
    public function regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson
    {
        $this->history->snapshot($lesson);

        $new_embedding = $this->generator->createEmbedding($dto);

        $similar_lesson = $this->similarity->findSimilar($lesson->user_id, $new_embedding);
        if ($similar_lesson && $similar_lesson->id !== $lesson->id) {
            throw new LessonExistsException($similar_lesson);
        }

        $new_data = $this->generator->generateLesson($dto);

        $lesson->update([
            'lesson_name' => $new_data['lesson_name'],
            'lesson_text' => $new_data['lesson_text'],
            'language_level' => $dto->level,
            'lesson_lang' => $dto->learning_lang,
            'grammar_data' => $new_data['grammar_highlights'],
            'vocabulary' => $new_data['vocabulary'],
            'dialogue' => $new_data['dialogue'] ?? [],
            'exercises' => $new_data['exercises'] ?? [],
            'homework' => $new_data['homework'] ?? null,
            'common_mistakes' => $new_data['common_mistakes'] ?? [],
            'culture_notes' => $new_data['culture_notes'] ?? null,
            'embedding' => $new_embedding,
        ]);

        return $lesson;
    }
}
