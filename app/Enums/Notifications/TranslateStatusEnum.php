<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum TranslateStatusEnum: string
{
    case SUCCESSFULLY = 'successfully';
    case INVALID = 'invalid';
}
