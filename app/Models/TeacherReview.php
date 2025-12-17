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
 * @property int $rating
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User $student
 * @property-read \App\Models\User $teacher
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherReview whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TeacherReview extends Model
{
    protected $table = TableNameEnum::TEACHER_REVIEWS->value;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
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
}
