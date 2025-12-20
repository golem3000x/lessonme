<?php

declare(strict_types=1);

namespace App\Models\Course;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User\User;
use Database\Factories\Course\CourseAssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_id
 * @property int $teacher_id
 * @property int $student_id
 * @property Carbon $assigned_at
 * @property Carbon|null $due_date
 * @property AssignmentStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Course $course
 * @property-read User $student
 * @property-read User $teacher
 * @method static \Database\Factories\Course\CourseAssignmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseAssignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseAssignment extends Model
{
    /** @use HasFactory<CourseAssignmentFactory> */
    use HasFactory;

    protected $table = TableNameEnum::COURSE_ASSIGNMENTS->value;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CourseAssignmentFactory
    {
        return CourseAssignmentFactory::new();
    }

    protected $fillable = [
        'course_id',
        'teacher_id',
        'student_id',
        'assigned_at',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'due_date' => 'datetime',
            'status' => AssignmentStatusEnum::class,
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

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && ! $this->status->isCompleted();
    }
}
