<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum LessonCreationStatusEnum: string
{
    case CREATED = 'created';
    case CREATION = 'creation';
    case REGENERATION = 'regeneration';
    case ARCHIVED = 'archived';
    case EXISTS = 'exists';
    case ERROR = 'error';

    /**
     * Get all values as array for database enum.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
