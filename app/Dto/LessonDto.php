<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\LanguageLevelEnum;

/**
 * Data Transfer Object (DTO) for Lesson entity.
 *
 * This class serves as a data structure to transfer lesson-related data
 * between different layers of the application. It helps maintain data integrity
 * and provides a clear contract for the data structure.
 *
 * @property string $topic Topic of the lesson (e.g., 'programming', 'cooking', 'travel', 'art')
 * @property LanguageLevelEnum $level The level of the lesson
 * @property string $native_lang The native language of the lesson
 * @property string $learning_lang The learning language of the lesson
 * @property array<int, string> $keywords The keywords of the lesson
 */
class LessonDto
{
    /**
     * @param array<int, string> $keywords
     */
    public function __construct(
        public string            $topic,
        public LanguageLevelEnum $level,
        public string            $native_lang,
        public string            $learning_lang,
        public array             $keywords
    )
    {
    }

    public function levelValue(): string
    {
        return $this->level->value;
    }

    public function getKeywordsAsString(): string
    {
        return count($this->keywords)
            ? implode(', ', $this->keywords)
            : '';
    }
}
