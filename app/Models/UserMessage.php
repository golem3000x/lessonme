<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\MessageTypeEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $sender_id
 * @property int $recipient_id
 * @property string $content
 * @property MessageTypeEnum $message_type
 * @property Carbon|null $read_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $sender
 * @property-read User $recipient
 * @property-read bool $is_read
 * @property-read TeacherInvitation|null $invitation
 */
class UserMessage extends Model
{
    protected $table = TableNameEnum::USER_MESSAGES->value;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'content',
        'message_type',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'message_type' => MessageTypeEnum::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Check if message is read.
     */
    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Get the invitation associated with this message.
     *
     * @return HasOne<TeacherInvitation, $this>
     */
    public function invitation(): HasOne
    {
        return $this->hasOne(TeacherInvitation::class, 'message_id');
    }

    /**
     * Check if this is an invitation message.
     */
    public function isInvitation(): bool
    {
        return $this->message_type === MessageTypeEnum::INVITATION;
    }
}
