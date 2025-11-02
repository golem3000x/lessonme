<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\UserSlugVisibility;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $keyword_id
 * @property bool $is_hidden
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Keyword\Keyword $entity
 * @property-read \App\Models\Keyword\Keyword $keyword
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserKeywordVisibility whereUserId($value)
 * @mixin \Eloquent
 */
class UserKeywordVisibility extends UserSlugVisibility
{
    protected $table = TableNameEnum::USER_KEYWORD_VISIBILITY->value;

    protected array $extra_fillable = [
        'keyword_id',
    ];

    public function entity(): BelongsTo
    {
        return $this->keyword();
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
