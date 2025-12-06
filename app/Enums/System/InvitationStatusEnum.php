<?php

declare(strict_types=1);

namespace App\Enums\System;

enum InvitationStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('invitations.status_pending'),
            self::ACCEPTED => __('invitations.status_accepted'),
            self::DECLINED => __('invitations.status_declined'),
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    public function isDeclined(): bool
    {
        return $this === self::DECLINED;
    }
}
