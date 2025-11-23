<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\System\TableNameEnum;
use App\Models\Abstracts\Slug\Slug;
use App\Models\Topic\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @extends Slug<KeywordTranslation, UserKeywordVisibility>
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $topic_id
 * @property string $slug
 * @property bool $is_system
 * @property bool $is_approved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Topic|null $topic
 * @property-read Collection<int, \App\Models\Keyword\KeywordTranslation> $translations
 * @property-read int|null $translations_count
 * @property-read User|null $user
 * @property-read Collection<int, \App\Models\Keyword\UserKeywordVisibility> $visibility
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
class Keyword extends Slug
{
    protected $table = TableNameEnum::KEYWORDS->value;

    /** @var array<int, string> */
    protected array $extra_fillable = [
        'topic_id',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Keyword\KeywordFactory
    {
        return \Database\Factories\Keyword\KeywordFactory::new();
    }

    /**
     * @return HasMany<KeywordTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(KeywordTranslation::class, 'keyword_id', 'id');
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return HasMany<UserKeywordVisibility, $this>
     */
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
}
