<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\System\MessageTypeEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User\Teacher\TeacherInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $sender_id
 * @property int $recipient_id
 * @property string $content
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property MessageTypeEnum $message_type
 * @property-read bool $is_read
 * @property-read \App\Models\User\Teacher\TeacherInvitation|null $invitation
 * @property-read \App\Models\User\User $recipient
 * @property-read \App\Models\User\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereUpdatedAt($value)
 * @mixin \Eloquent
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
