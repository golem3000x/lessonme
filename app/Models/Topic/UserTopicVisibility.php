<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Slug\UserSlugVisibility;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @extends UserSlugVisibility<Topic>
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property bool $is_hidden
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Topic\Topic $entity
 * @property-read \App\Models\Topic\Topic $topic
 * @property-read User $user
 * @method static Builder<static>|UserTopicVisibility newModelQuery()
 * @method static Builder<static>|UserTopicVisibility newQuery()
 * @method static Builder<static>|UserTopicVisibility query()
 * @method static Builder<static>|UserTopicVisibility whereCreatedAt($value)
 * @method static Builder<static>|UserTopicVisibility whereId($value)
 * @method static Builder<static>|UserTopicVisibility whereIsHidden($value)
 * @method static Builder<static>|UserTopicVisibility whereTopicId($value)
 * @method static Builder<static>|UserTopicVisibility whereUpdatedAt($value)
 * @method static Builder<static>|UserTopicVisibility whereUserId($value)
 * @mixin \Eloquent
 */
class UserTopicVisibility extends UserSlugVisibility
{
    protected $table = TableNameEnum::USER_TOPIC_VISIBILITY->value;

    /** @var array<int, string> */
    protected array $extra_fillable = [
        'topic_id',
    ];

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function entity(): BelongsTo
    {
        return $this->topic();
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
