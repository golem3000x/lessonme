<?php

declare(strict_types=1);

namespace App\Models\Support;

use App\Enums\ChatSessionStatusEnum;
use App\Enums\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $last_message_at
 * @property int $unread_count
 * @property ChatSessionStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Message> $messages
 * @property-read int|null $messages_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUnreadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ChatSession extends Model
{
    protected $table = TableNameEnum::CHAT_SESSIONS->value;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'last_message_at',
        'unread_count',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'unread_count' => 'integer',
            'status' => ChatSessionStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
