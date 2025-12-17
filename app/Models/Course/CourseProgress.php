<?php

declare(strict_types=1);

namespace App\Models\Course;

use App\Enums\System\TableNameEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_id
 * @property int $user_id
 * @property int $lesson_id
 * @property int $score
 * @property bool $is_passed
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Course\Course $course
 * @property-read Lesson $lesson
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereIsPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseProgress whereUserId($value)
 *
 * @mixin \Eloquent
 */
class CourseProgress extends Model
{
    protected $table = TableNameEnum::COURSE_PROGRESS->value;

    protected $fillable = [
        'course_id',
        'user_id',
        'lesson_id',
        'score',
        'is_passed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'is_passed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Lesson, $this>
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark as passed with score.
     */
    public function markAsPassed(int $score): void
    {
        $this->update([
            'score' => $score,
            'is_passed' => true,
            'completed_at' => now(),
        ]);
    }
}
