<?php

declare(strict_types=1);

namespace App\Models\Keyword;

use App\Enums\TableNameEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeywordTranslation extends Model
{
    use HasFactory;

    protected $table = TableNameEnum::KEYWORD_TRANSLATIONS->value;

    protected $fillable = [
        'keyword_id',
        'language_code',
        'value',
    ];

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
