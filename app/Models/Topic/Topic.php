<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Slug\Slug;
use App\Models\Keyword\Keyword;
use App\Models\Lesson\Lesson;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @extends Slug<TopicTranslation, UserTopicVisibility>
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $slug
 * @property bool $is_system
 * @property bool $is_approved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Keyword> $keywords
 * @property-read int|null $keywords_count
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @property-read Collection<int, \App\Models\Topic\TopicTranslation> $translations
 * @property-read int|null $translations_count
 * @property-read User|null $user
 * @property-read Collection<int, \App\Models\Topic\UserTopicVisibility> $visibility
 * @property-read int|null $visibility_count
 *
 * @method static \Database\Factories\Topic\TopicFactory factory($count = null, $state = [])
 * @method static Builder<static>|Topic newModelQuery()
 * @method static Builder<static>|Topic newQuery()
 * @method static Builder<static>|Topic query()
 * @method static Builder<static>|Topic visibleForUser(int $userId)
 * @method static Builder<static>|Topic whereCreatedAt($value)
 * @method static Builder<static>|Topic whereId($value)
 * @method static Builder<static>|Topic whereIsApproved($value)
 * @method static Builder<static>|Topic whereIsSystem($value)
 * @method static Builder<static>|Topic whereSlug($value)
 * @method static Builder<static>|Topic whereUpdatedAt($value)
 * @method static Builder<static>|Topic whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Topic extends Slug
{
    protected $table = TableNameEnum::TOPICS->value;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Topic\TopicFactory
    {
        return \Database\Factories\Topic\TopicFactory::new();
    }

    /**
     * @return BelongsToMany<Lesson, $this>
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * @return HasMany<TopicTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TopicTranslation::class, 'topic_id', 'id');
    }

    /**
     * @return HasMany<Keyword, $this>
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * @return HasMany<UserTopicVisibility, $this>
     */
    public function visibility(): HasMany
    {
        return $this->hasMany(UserTopicVisibility::class);
    }

    /**
     * Get title in specific language (alias for translatedValue)
     */
    public function title(?string $lang = null): string
    {
        return $this->translatedValue($lang);
    }
}
