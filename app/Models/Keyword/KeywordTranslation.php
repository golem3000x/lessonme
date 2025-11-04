<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\SlugTranslation;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $keyword_id
 * @property string $language_code
 * @property string $value
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Keyword $entity
 * @property-read Keyword $keyword
 *
 * @method static Builder<static>|KeywordTranslation newModelQuery()
 * @method static Builder<static>|KeywordTranslation newQuery()
 * @method static Builder<static>|KeywordTranslation query()
 * @method static Builder<static>|KeywordTranslation whereCreatedAt($value)
 * @method static Builder<static>|KeywordTranslation whereId($value)
 * @method static Builder<static>|KeywordTranslation whereKeywordId($value)
 * @method static Builder<static>|KeywordTranslation whereLanguageCode($value)
 * @method static Builder<static>|KeywordTranslation whereUpdatedAt($value)
 * @method static Builder<static>|KeywordTranslation whereValue($value)
 *
 * @mixin Eloquent
 */
class KeywordTranslation extends SlugTranslation
{
    protected $table = TableNameEnum::KEYWORD_TRANSLATIONS->value;

    protected array $extra_fillable = [
        'keyword_id',
    ];

    public function entity(): BelongsTo
    {
        return $this->keyword();
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class, 'keyword_id', 'id');
    }
}
