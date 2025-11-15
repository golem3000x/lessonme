<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Models\Lesson\Lesson;

class LessonSimilarityService
{
    /**
     * Найти похожий урок для пользователя по вектору embedding.
     *
     * @param  array<float>  $embedding
     */
    public function findSimilar(int $user_id, array $embedding): ?Lesson
    {
        $embedding_str = '['.implode(',', $embedding).']';

        // Используем pgvector оператор <=>; similarity = 1 - distance
        return Lesson::selectRaw('*, 1 - (embedding <=> ?) as similarity_score', [$embedding_str])
            ->where('user_id', $user_id)
            ->whereRaw('1 - (embedding <=> ?) >= ?', [$embedding_str, Lesson::SIMILARITY_THRESHOLD])
            ->orderByDesc('similarity_score')
            ->first();
    }
}
