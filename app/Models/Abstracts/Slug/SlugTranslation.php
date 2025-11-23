<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Slug;

use App\Models\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @template TEntity of Slug
 * 
 * @property string $language_code
 * @property string $value
 */
abstract class SlugTranslation extends BaseModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'language_code',
        'value',
    ];

    public $timestamps = false;

    /**
     * Relation to parent entity (Topic, Keyword, etc.)
     * 
     * @return BelongsTo<TEntity, $this>
     */
    abstract public function entity(): BelongsTo;
}
