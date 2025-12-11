<?php

declare(strict_types=1);

namespace App\Enums\Lesson;

enum LessonCreationTypeEnum: string
{
    case AI = 'ai';
    case MANUAL = 'manual';

    /**
     * Get translated label for the creation type.
     */
    public function label(): string
    {
        return match ($this) {
            self::AI => __('lessons.creation_type_ai'),
            self::MANUAL => __('lessons.creation_type_manual'),
        };
    }

    /**
     * Get icon for the creation type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::AI => 'sparkles',
            self::MANUAL => 'pencil',
        };
    }

    /**
     * Check if this is an AI-generated lesson.
     */
    public function isAi(): bool
    {
        return $this === self::AI;
    }

    /**
     * Check if this is a manually created lesson.
     */
    public function isManual(): bool
    {
        return $this === self::MANUAL;
    }
}
