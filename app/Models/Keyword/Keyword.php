<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Models\Topic\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $topic_id
 * @property string $slug
 * @property int $is_system
 * @property int $is_approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Topic|null $topic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Keyword\KeywordTranslation> $translations
 * @property-read int|null $translations_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Keyword\UserKeywordVisibility> $visibility
 * @property-read int|null $visibility_count
 * @method static Builder<static>|Keyword newModelQuery()
 * @method static Builder<static>|Keyword newQuery()
 * @method static Builder<static>|Keyword query()
 * @method static Builder<static>|Keyword visibleForUser(int $userId)
 * @method static Builder<static>|Keyword whereCreatedAt($value)
 * @method static Builder<static>|Keyword whereId($value)
 * @method static Builder<static>|Keyword whereIsApproved($value)
 * @method static Builder<static>|Keyword whereIsSystem($value)
 * @method static Builder<static>|Keyword whereSlug($value)
 * @method static Builder<static>|Keyword whereTopicId($value)
 * @method static Builder<static>|Keyword whereUpdatedAt($value)
 * @method static Builder<static>|Keyword whereUserId($value)
 * @mixin \Eloquent
 */
class Keyword extends SlugEntity
{
    protected $table = TableNameEnum::KEYWORDS->value;

    protected array $extra_fillable = [
        'topic_id',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(KeywordTranslation::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function visibility(): HasMany
    {
        return $this->hasMany(UserKeywordVisibility::class);
    }

    /**
     * Get value in specific language (alias for translatedValue)
     */
    public function value(?string $lang = null): string
    {
        return $this->translatedValue($lang);
    }

    /**
     * Scope to get keywords visible for specific user
     * Includes check for parent topic visibility
     */
    public function scopeVisibleForUser(Builder $query, int $userId): Builder
    {
        return $query
            ->whereHas('topic', function (Builder $topicQuery) use ($userId): void {
                $topicQuery->visibleForUser($userId);
            })
            ->where(function (Builder $q) use ($userId): void {
                $q->where(function (Builder $sys) use ($userId): void {
                    $sys->whereNull('user_id')
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', $userId)
                                ->where('is_hidden', true);
                        });
                })->orWhere(function (Builder $own) use ($userId): void {
                    $own->where('user_id', $userId)
                        ->whereDoesntHave('visibility', function (Builder $v) use ($userId): void {
                            $v->where('user_id', $userId)
                                ->where('is_hidden', true);
                        })
                        ->where('is_approved', true);
                });
            });
    }
}
