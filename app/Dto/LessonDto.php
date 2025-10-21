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
 * @property string $topic Topic of the lesson (e.g., 'programming', 'cooking', 'travel', 'art')
 * @property string $level The level of the lesson (e.g., 'beginner', 'intermediate', 'advanced')
 * @property string $native_language The native language of the lesson
 * @property string $learning_lang The learning language of the lesson
 * @property array $themes The themes of the lesson
 */
class LessonDto
{
    public function __construct(
        public string $topic,
        public string $level,
        public string $native_language,
        public string $learning_lang,
        public array  $themes
    )
    {
    }
}
