<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\FeedbackRatingEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $lesson_history_id
 * @property int $lesson_id
 * @property int $user_id
 * @property FeedbackRatingEnum $rating
 * @property string|null $comment
 * @property int|null $original_feedback_id
 * @property Carbon|null $original_created_at
 * @property Carbon|null $archived_at
 * @property-read Lesson $lesson
 * @property-read LessonHistory|null $lessonHistory
 * @property-read User $user
 */
class LessonFeedbackHistory extends Model
{
    protected $table = TableNameEnum::LESSON_FEEDBACK_HISTORY->value;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lesson_history_id',
        'lesson_id',
        'user_id',
        'rating',
        'comment',
        'original_feedback_id',
        'original_created_at',
        'archived_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => FeedbackRatingEnum::class,
            'original_created_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Lesson, $this>
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * @return BelongsTo<LessonHistory, $this>
     */
    public function lessonHistory(): BelongsTo
    {
        return $this->belongsTo(LessonHistory::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
