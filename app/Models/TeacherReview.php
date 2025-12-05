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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $teacher
 * @property-read User $student
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
