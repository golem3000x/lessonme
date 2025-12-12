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
 * @property int|null $original_feedback_id ID of original feedback before archiving
 * @property Carbon|null $original_created_at Original creation timestamp
 * @property Carbon $archived_at When feedback was moved to history
 * @property-read \App\Models\Lesson\Lesson $lesson
 * @property-read \App\Models\Lesson\LessonHistory|null $lessonHistory
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereLessonHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereOriginalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereOriginalFeedbackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedbackHistory whereUserId($value)
 * @mixin \Eloquent
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
