<?php

declare(strict_types=1);

namespace App\Models\Abstracts\Slug;

use App\Models\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class SlugTranslation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'language_code',
        'value',
    ];

    public $timestamps = false;

    /**
     * Relation to parent entity (Topic, Keyword, etc.)
     */
    abstract public function entity(): BelongsTo;
}
