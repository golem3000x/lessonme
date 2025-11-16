<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\UserSlugVisibility;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $keyword_id
 * @property bool $is_hidden
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Keyword $entity
 * @property-read Keyword $keyword
 * @property-read User $user
 * @method static Builder<static>|UserKeywordVisibility newModelQuery()
 * @method static Builder<static>|UserKeywordVisibility newQuery()
 * @method static Builder<static>|UserKeywordVisibility query()
 * @method static Builder<static>|UserKeywordVisibility whereCreatedAt($value)
 * @method static Builder<static>|UserKeywordVisibility whereId($value)
 * @method static Builder<static>|UserKeywordVisibility whereIsHidden($value)
 * @method static Builder<static>|UserKeywordVisibility whereKeywordId($value)
 * @method static Builder<static>|UserKeywordVisibility whereUpdatedAt($value)
 * @method static Builder<static>|UserKeywordVisibility whereUserId($value)
 * @mixin Eloquent
 * @mixin \Eloquent
 */
class UserKeywordVisibility extends UserSlugVisibility
{
    protected $table = TableNameEnum::USER_KEYWORD_VISIBILITY->value;

    /** @var array<int, string> */
    protected array $extra_fillable = [
        'keyword_id',
    ];

    /**
     * @return BelongsTo<Keyword, $this>
     */
    public function entity(): BelongsTo
    {
        return $this->keyword();
    }

    /**
     * @return BelongsTo<Keyword, $this>
     */
    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
