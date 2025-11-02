<?php

declare(strict_types=1);

namespace App\Enums;

enum ModerationStatusEnum: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case INVALID = 'invalid';
}
