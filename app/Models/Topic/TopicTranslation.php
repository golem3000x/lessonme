<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Slug\SlugTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @extends SlugTranslation<Topic>
 *
 * @property int $id
 * @property int $topic_id
 * @property string $language_code
 * @property string $value
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Topic\Topic $entity
 * @property-read \App\Models\Topic\Topic $topic
 *
 * @method static Builder<static>|TopicTranslation newModelQuery()
 * @method static Builder<static>|TopicTranslation newQuery()
 * @method static Builder<static>|TopicTranslation query()
 * @method static Builder<static>|TopicTranslation whereCreatedAt($value)
 * @method static Builder<static>|TopicTranslation whereId($value)
 * @method static Builder<static>|TopicTranslation whereLanguageCode($value)
 * @method static Builder<static>|TopicTranslation whereTopicId($value)
 * @method static Builder<static>|TopicTranslation whereUpdatedAt($value)
 * @method static Builder<static>|TopicTranslation whereValue($value)
 *
 * @mixin \Eloquent
 */
class TopicTranslation extends SlugTranslation
{
    protected $table = TableNameEnum::TOPIC_TRANSLATIONS->value;

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
        return $this->belongsTo(Topic::class, 'topic_id', 'id');
    }
}
