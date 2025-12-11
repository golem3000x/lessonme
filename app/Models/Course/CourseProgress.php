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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Course $course
 * @property-read User $user
 * @property-read Lesson $lesson
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
