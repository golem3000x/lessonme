<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationEventTypeEnum: string
{
    case MODERATION = 'moderation';
    case TRANSLATE = 'translate';
}
