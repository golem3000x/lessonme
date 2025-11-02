<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Slug\SlugTranslation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $keyword_id
 * @property string $language_code
 * @property string $value
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Keyword\Keyword $entity
 * @property-read \App\Models\Keyword\Keyword $keyword
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeywordTranslation whereValue($value)
 * @mixin \Eloquent
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
        return $this->belongsTo(Keyword::class);
    }
}
