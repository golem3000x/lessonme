<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum ModerationStatusEnum: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case INVALID = 'invalid';
    case PENDING = 'pending';
}
