<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $teacher_id
 * @property int $student_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User $student
 * @property-read \App\Models\User $teacher
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherStudent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TeacherStudent extends Model
{
    protected $table = TableNameEnum::TEACHER_STUDENT->value;

    protected $fillable = [
        'teacher_id',
        'student_id',
    ];

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
}
