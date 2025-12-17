<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User;
use Database\Factories\Lesson\LessonAssignmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lesson_id
 * @property int $teacher_id
 * @property int $student_id
 * @property array<array-key, mixed>|null $assigned_version Snapshot of exercises at assignment time
 * @property Carbon $assigned_at
 * @property Carbon|null $due_date
 * @property AssignmentStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\Lesson\LessonAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read \App\Models\Lesson\Lesson $lesson
 * @property-read User $student
 * @property-read User $teacher
 *
 * @method static \Database\Factories\Lesson\LessonAssignmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|LessonAssignment newModelQuery()
 * @method static Builder<static>|LessonAssignment newQuery()
 * @method static Builder<static>|LessonAssignment query()
 * @method static Builder<static>|LessonAssignment whereAssignedAt($value)
 * @method static Builder<static>|LessonAssignment whereAssignedVersion($value)
 * @method static Builder<static>|LessonAssignment whereCreatedAt($value)
 * @method static Builder<static>|LessonAssignment whereDueDate($value)
 * @method static Builder<static>|LessonAssignment whereId($value)
 * @method static Builder<static>|LessonAssignment whereLessonId($value)
 * @method static Builder<static>|LessonAssignment whereStatus($value)
 * @method static Builder<static>|LessonAssignment whereStudentId($value)
 * @method static Builder<static>|LessonAssignment whereTeacherId($value)
 * @method static Builder<static>|LessonAssignment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LessonAssignment extends Model
{
    /** @use HasFactory<LessonAssignmentFactory> */
    use HasFactory;

    protected $table = TableNameEnum::LESSON_ASSIGNMENTS->value;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): LessonAssignmentFactory
    {
        return LessonAssignmentFactory::new();
    }

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
     * @return HasMany<LessonAttempt, $this>
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
