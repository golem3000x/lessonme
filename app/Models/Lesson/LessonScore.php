<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $attempt_id
 * @property int $lesson_id
 * @property int $user_id
 * @property int|null $teacher_id
 * @property int $total_questions
 * @property int $correct_answers
 * @property float $score_percentage
 * @property bool $auto_graded
 * @property string|null $teacher_comment
 * @property Carbon $graded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LessonAttempt $attempt
 * @property-read Lesson $lesson
 * @property-read User $user
 * @property-read User|null $teacher
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTotalQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereScorePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereAutoGraded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTeacherComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereGradedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LessonScore extends Model
{
    protected $table = TableNameEnum::LESSON_SCORES->value;

    protected $fillable = [
        'attempt_id',
        'lesson_id',
        'user_id',
        'teacher_id',
        'total_questions',
        'correct_answers',
        'score_percentage',
        'auto_graded',
        'teacher_comment',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'score_percentage' => 'float',
            'auto_graded' => 'boolean',
            'graded_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<LessonAttempt, $this>
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(LessonAttempt::class);
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
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function isPerfectScore(): bool
    {
        return $this->score_percentage >= 100.0;
    }

    public function isPassingGrade(float $passing_percentage = 60.0): bool
    {
        return $this->score_percentage >= $passing_percentage;
    }

    public function getGradeLabel(): string
    {
        return match (true) {
            $this->score_percentage >= 90.0 => 'A',
            $this->score_percentage >= 80.0 => 'B',
            $this->score_percentage >= 70.0 => 'C',
            $this->score_percentage >= 60.0 => 'D',
            default => 'F',
        };
    }
}
