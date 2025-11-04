<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\UserSlugVisibility;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property bool $is_hidden
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Topic $entity
 * @property-read Topic $topic
 * @property-read User $user
 *
 * @method static Builder<static>|UserTopicVisibility newModelQuery()
 * @method static Builder<static>|UserTopicVisibility newQuery()
 * @method static Builder<static>|UserTopicVisibility query()
 * @method static Builder<static>|UserTopicVisibility whereCreatedAt($value)
 * @method static Builder<static>|UserTopicVisibility whereId($value)
 * @method static Builder<static>|UserTopicVisibility whereIsHidden($value)
 * @method static Builder<static>|UserTopicVisibility whereTopicId($value)
 * @method static Builder<static>|UserTopicVisibility whereUpdatedAt($value)
 * @method static Builder<static>|UserTopicVisibility whereUserId($value)
 *
 * @mixin Eloquent
 */
class UserTopicVisibility extends UserSlugVisibility
{
    protected $table = TableNameEnum::USER_TOPIC_VISIBILITY->value;

    protected array $extra_fillable = [
        'topic_id',
    ];

    public function entity(): BelongsTo
    {
        return $this->topic();
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
