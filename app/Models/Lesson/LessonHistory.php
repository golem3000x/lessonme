<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Casts\EmbeddingCast;
use App\Enums\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonHistory extends Model
{
    protected $table = TableNameEnum::LESSON_HISTORIES->value;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'topic_id',
        'lesson_name',
        'lesson_text',
        'grammar_data',
        'vocabulary',
        'quiz',
        'embedding',
    ];

    protected $casts = [
        'grammar_data' => 'array',
        'vocabulary' => 'array',
        'quiz' => 'array',
        'embedding' => EmbeddingCast::class,
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
