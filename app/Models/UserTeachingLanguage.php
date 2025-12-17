<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property string $language
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Enums\System\SystemLanguageEnum|null $language_enum
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTeachingLanguage whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserTeachingLanguage extends Model
{
    protected $table = TableNameEnum::USER_TEACHING_LANGUAGES->value;

    public $incrementing = false;

    protected $primaryKey = ['user_id', 'language'];

    protected $fillable = [
        'user_id',
        'language',
    ];

    /**
     * Get the user that owns this teaching language.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the language as enum.
     */
    public function getLanguageEnumAttribute(): ?SystemLanguageEnum
    {
        return SystemLanguageEnum::tryFrom($this->language);
    }
}
