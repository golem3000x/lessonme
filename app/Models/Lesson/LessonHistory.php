<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\TableNameEnum;
use App\Models\Abstracts\Lesson\BaseLesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonHistory extends BaseLesson
{
    protected $table = TableNameEnum::LESSON_HISTORIES->value;

    protected array $extra_fillable = [
        'lesson_id',
    ];

    // Other fillable and casts inherited from BaseLesson

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // user() method inherited from BaseLesson
}
