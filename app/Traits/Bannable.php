<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Trait for managing user banning functionality.
 *
 * @property bool $is_banned
 * @property Carbon|null $banned_at
 * @property int|null $banned_by
 * @property string|null $ban_reason
 * @property Carbon|null $ban_expires_at
 */
trait Bannable
{
    /**
     * Get the admin who banned this user.
     *
     * @return BelongsTo<User, $this>
     */
    public function bannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Check if user is currently banned.
     */
    public function isBanned(): bool
    {
        if (! $this->is_banned) {
            return false;
        }

        // Check if ban has expired
        if ($this->ban_expires_at && $this->ban_expires_at->isPast()) {
            $this->unban();

            return false;
        }

        return true;
    }

    /**
     * Ban the user.
     */
    public function ban(?string $reason = null, ?Carbon $expires_at = null, ?int $banned_by = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'banned_by' => $banned_by,
            'ban_reason' => $reason,
            'ban_expires_at' => $expires_at,
        ]);
    }

    /**
     * Unban the user.
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_by' => null,
            'ban_reason' => null,
            'ban_expires_at' => null,
        ]);
    }
}
