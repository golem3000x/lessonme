<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $teacher_id
 * @property int $student_id
 * @property int|null $message_id
 * @property InvitationStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $teacher
 * @property-read User $student
 * @property-read UserMessage|null $message
 */
class TeacherInvitation extends Model
{
    protected $table = TableNameEnum::TEACHER_INVITATIONS->value;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'message_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvitationStatusEnum::class,
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

    /**
     * @return BelongsTo<UserMessage, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(UserMessage::class, 'message_id');
    }

    public function accept(): void
    {
        $this->update(['status' => InvitationStatusEnum::ACCEPTED->value]);

        // Create teacher-student relationship
        $this->teacher->addStudent($this->student);
    }

    public function decline(): void
    {
        $this->update(['status' => InvitationStatusEnum::DECLINED->value]);
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }
}
