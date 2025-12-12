<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\FeedbackRatingEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User;
use Database\Factories\Lesson\LessonFeedbackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @use HasFactory<LessonFeedbackFactory>
 * @property int $id
 * @property int $lesson_id
 * @property int $user_id
 * @property FeedbackRatingEnum $rating
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Lesson\Lesson $lesson
 * @property-read User $user
 * @method static \Database\Factories\Lesson\LessonFeedbackFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonFeedback whereUserId($value)
 * @mixin \Eloquent
 */
class LessonFeedback extends Model
{
    /** @use HasFactory<LessonFeedbackFactory> */
    use HasFactory;

    protected $table = TableNameEnum::LESSON_FEEDBACKS->value;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lesson_id',
        'user_id',
        'rating',
        'comment',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => FeedbackRatingEnum::class,
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
