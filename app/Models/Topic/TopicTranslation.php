<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\SlugTranslation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $topic_id
 * @property string $language_code
 * @property string $value
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Topic\Topic $entity
 * @property-read \App\Models\Topic\Topic $topic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicTranslation whereValue($value)
 * @mixin \Eloquent
 */
class TopicTranslation extends SlugTranslation
{
    protected $table = TableNameEnum::TOPIC_TRANSLATIONS->value;

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
