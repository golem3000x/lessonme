<?php

declare(strict_types=1);

namespace App\Models\Topic;

use App\Enums\TableNameEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicTranslation extends Model
{
    use HasFactory;

    protected $table = TableNameEnum::TOPIC_TRANSLATIONS->value;

    protected $fillable = [
        'topic_id',
        'language_code',
        'value',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
