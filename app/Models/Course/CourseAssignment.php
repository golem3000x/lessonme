<?php

declare(strict_types=1);

namespace App\Models\Course;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User\User;
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
 */
class CourseAssignment extends Model
{
    protected $table = TableNameEnum::COURSE_ASSIGNMENTS->value;

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
