<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\System\TableNameEnum;
use App\Models\User;
use Database\Factories\Lesson\LessonScoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @use HasFactory<LessonScoreFactory>
 *
 * @property int $id
 * @property int $attempt_id
 * @property int $lesson_id
 * @property int $user_id
 * @property int|null $teacher_id For manual grading by teacher
 * @property int $total_questions
 * @property int $correct_answers
 * @property float $score_percentage 0.00 to 100.00
 * @property bool $auto_graded
 * @property string|null $teacher_comment
 * @property Carbon $graded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Lesson\LessonAttempt $attempt
 * @property-read \App\Models\Lesson\Lesson $lesson
 * @property-read User|null $teacher
 * @property-read User $user
 *
 * @method static \Database\Factories\Lesson\LessonScoreFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereAutoGraded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereGradedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereScorePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTeacherComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereTotalQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonScore whereUserId($value)
 *
 * @mixin \Eloquent
 */
class LessonScore extends Model
{
    /** @use HasFactory<LessonScoreFactory> */
    use HasFactory;

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
