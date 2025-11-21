<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\AssignmentStatusEnum;
use App\Enums\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lesson_id
 * @property int $teacher_id
 * @property int $student_id
 * @property array<string, mixed>|null $assigned_version
 * @property Carbon $assigned_at
 * @property Carbon|null $due_date
 * @property AssignmentStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Lesson $lesson
 * @property-read User $teacher
 * @property-read User $student
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LessonAttempt> $attempts
 * @property-read int|null $attempts_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereAssignedVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonAssignment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LessonAssignment extends Model
{
    protected $table = TableNameEnum::LESSON_ASSIGNMENTS->value;

    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'student_id',
        'assigned_version',
        'assigned_at',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_version' => 'array',
            'assigned_at' => 'datetime',
            'due_date' => 'datetime',
            'status' => AssignmentStatusEnum::class,
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
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * @return HasMany<LessonAttempt>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(LessonAttempt::class, 'assignment_id');
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null 
            && $this->due_date->isPast() 
            && ! $this->status->isCompleted();
    }
}
