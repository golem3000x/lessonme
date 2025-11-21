<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lesson_id
 * @property int $user_id
 * @property int|null $assignment_id
 * @property int $attempt_number
 * @property array<string, mixed>|null $exercises_snapshot
 * @property array<string, mixed>|null $user_answers
 * @property Carbon $started_at
 * @property Carbon|null $completed_at
 * @property bool $is_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Lesson $lesson
 * @property-read User $user
 * @property-read LessonAssignment|null $assignment
 * @property-read LessonScore|null $score
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereAttemptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereExercisesSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereUserAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAttempt whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LessonAttempt extends Model
{
    protected $table = TableNameEnum::LESSON_ATTEMPTS->value;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'assignment_id',
        'attempt_number',
        'exercises_snapshot',
        'user_answers',
        'started_at',
        'completed_at',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'exercises_snapshot' => 'array',
            'user_answers' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_completed' => 'boolean',
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

    /**
     * @return BelongsTo<LessonAssignment, $this>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(LessonAssignment::class);
    }

    /**
     * @return HasOne<LessonScore>
     */
    public function score(): HasOne
    {
        return $this->hasOne(LessonScore::class, 'attempt_id');
    }

    public function getDurationInMinutes(): ?int
    {
        if ($this->completed_at === null) {
            return null;
        }

        return (int) $this->started_at->diffInMinutes($this->completed_at);
    }
}
