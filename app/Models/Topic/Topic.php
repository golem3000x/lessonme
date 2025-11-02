<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Models\Keyword\Keyword;
use App\Models\Lesson;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Lesson> $lessons
 * @property-read int|null $lessons_count
 * @method static Builder<static>|Topic newModelQuery()
 * @method static Builder<static>|Topic newQuery()
 * @method static Builder<static>|Topic query()
 * @method static Builder<static>|Topic whereCreatedAt($value)
 * @method static Builder<static>|Topic whereId($value)
 * @method static Builder<static>|Topic whereNameId($value)
 * @method static Builder<static>|Topic whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int|null $user_id
 * @property int $is_system
 * @property int $is_approved
 * @property-read Collection<int, Keyword> $keywords
 * @property-read int|null $keywords_count
 * @property-read Collection<int, \App\Models\Topic\TopicTranslation> $translations
 * @property-read int|null $translations_count
 * @property-read \App\Models\User|null $user
 * @property-read Collection<int, \App\Models\Topic\UserTopicVisibility> $visibility
 * @property-read int|null $visibility_count
 * @method static Builder<static>|Topic visibleForUser(int $userId)
 * @method static Builder<static>|Topic whereIsApproved($value)
 * @method static Builder<static>|Topic whereIsSystem($value)
 * @method static Builder<static>|Topic whereSlug($value)
 * @method static Builder<static>|Topic whereUserId($value)
 * @mixin \Eloquent
 */
class Topic extends SlugEntity
{
    protected $table = TableNameEnum::TOPICS->value;

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TopicTranslation::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

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
