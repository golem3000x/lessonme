<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\UserSlugVisibility;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property bool $is_hidden
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Topic\Topic $entity
 * @property-read \App\Models\Topic\Topic $topic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTopicVisibility whereUserId($value)
 * @mixin \Eloquent
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
