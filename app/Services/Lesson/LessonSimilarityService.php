<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Models\Lesson\Lesson;

class LessonSimilarityService
{
    /**
     * Find a similar lesson for the user by embedding vector.
     *
     * @param  array<float>  $embedding
     */
    public function findSimilar(int $user_id, array $embedding, LessonDto $dto): ?Lesson
    {
        $embedding_str = '['.implode(',', $embedding).']';

        // Use pgvector operator <=>; similarity = 1 - distance
        return Lesson::selectRaw('*, 1 - (embedding <=> ?) as similarity_score', [$embedding_str])
            ->where('user_id', '=', $user_id)
            ->where('language_level', '=', $dto->levelValue())
            ->whereRaw('1 - (embedding <=> ?) >= ?', [$embedding_str, Lesson::SIMILARITY_THRESHOLD])
            ->orderByDesc('similarity_score')
            ->first();
    }
}
