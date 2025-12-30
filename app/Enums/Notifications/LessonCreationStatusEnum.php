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
    case PENDING_MODERATION = 'pending_moderation';
    case REJECTED = 'rejected';

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
