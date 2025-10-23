<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Data Transfer Object (DTO) for Lesson entity.
 *
 * This class serves as a data structure to transfer lesson-related data
 * between different layers of the application. It helps maintain data integrity
 * and provides a clear contract for the data structure.
 *
 * @property int $topic_id Topic of the lesson (e.g., 'programming', 'cooking', 'travel', 'art')
 * @property string $level The level of the lesson (e.g., 'beginner', 'intermediate', 'advanced')
 * @property int $native_language_id The native language of the lesson
 * @property int $learning_lang_id The learning language of the lesson
 * @property array $themes The themes of the lesson
 */
class LessonDto
{
    public function __construct(
        public int $topic_id,
        public string $level,
        public int $native_language_id,
        public int $learning_lang_id,
        public array $themes
    ) {}
}
