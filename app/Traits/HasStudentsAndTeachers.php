<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\System\TableNameEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait for managing teacher-student relationships.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $students
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $teachers
 */
trait HasStudentsAndTeachers
{
    /**
     * Students of this teacher.
     *
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TableNameEnum::TEACHER_STUDENT->value,
            'teacher_id',
            'student_id'
        )->withTimestamps();
    }

    /**
     * Teachers of this student.
     *
     * @return BelongsToMany<User, $this>
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TableNameEnum::TEACHER_STUDENT->value,
            'student_id',
            'teacher_id'
        )->withTimestamps();
    }

    /**
     * Add a student to this teacher's list.
     */
    public function addStudent(User $student): void
    {
        if (! $this->students()->where('student_id', '=', $student->id)->exists()) {
            $this->students()->attach($student->id);
        }
    }

    /**
     * Remove a student from this teacher's list.
     */
    public function removeStudent(User $student): void
    {
        $this->students()->detach($student->id);
    }
}
