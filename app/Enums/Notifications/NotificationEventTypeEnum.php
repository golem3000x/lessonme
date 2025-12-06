<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum NotificationEventTypeEnum: string
{
    case MODERATION = 'moderation';
    case TRANSLATE = 'translate';
    case CREATION = 'creation';
    case REGENERATION = 'regeneration';
    case ASSIGNMENT = 'assignment';
}
