<?php

declare(strict_types=1);

namespace App\Enums\System;

enum MessageTypeEnum: string
{
    case TEXT = 'text';
    case INVITATION = 'invitation';

    public function isInvitation(): bool
    {
        return $this === self::INVITATION;
    }
}
