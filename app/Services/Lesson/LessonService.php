<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Enums\Notifications\LessonCreationStatusEnum;
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
     * Fill existing lesson (placeholder) with generated content.
     *
     * @throws LessonExistsException
     */
    public function fillLessonContent(Lesson $lesson, LessonDto $dto): Lesson
    {
        $embedding = $this->generator->createEmbedding($dto);

        $similar_lesson = $this->similarity->findSimilar($lesson->user_id, $embedding, $dto);
        if ($similar_lesson && $similar_lesson->id !== $lesson->id) {
            throw new LessonExistsException($similar_lesson);
        }

        $data = $this->generator->generateLesson($dto);

        $lesson->update([
            'lesson_name' => $data['lesson_name'],
            'lesson_text' => $data['lesson_text'],
            'grammar_data' => $data['grammar_highlights'],
            'vocabulary' => $data['vocabulary'],
            'dialogue' => $data['dialogue'] ?? [],
            'exercises' => $data['exercises'] ?? [],
            'homework' => $data['homework'] ?? null,
            'common_mistakes' => $data['common_mistakes'] ?? [],
            'culture_notes' => $data['culture_notes'] ?? null,
            'embedding' => $embedding,
            // Note: status is NOT set here - it should be set by the job after all operations complete
        ]);

        return $lesson;
    }

    /**
     * @throws LessonExistsException
     */
    public function regenerateLesson(Lesson $lesson, LessonDto $dto): Lesson
    {
        $this->history->snapshot($lesson);

        $new_embedding = $this->generator->createEmbedding($dto);

        $similar_lesson = $this->similarity->findSimilar($lesson->user_id, $new_embedding, $dto);
        if ($similar_lesson && $similar_lesson->id !== $lesson->id) {
            $lesson->update(['status' => LessonCreationStatusEnum::CREATED]);
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
            // Note: status is NOT set here - it should be set by the job after all operations complete
        ]);

        return $lesson;
    }
}
