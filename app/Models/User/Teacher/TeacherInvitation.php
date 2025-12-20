<?php

declare(strict_types=1);

namespace App\Models\User\Teacher;

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\TableNameEnum;
use App\Models\User\User;
use App\Models\User\UserMessage;
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
 * @property-read \App\Models\User\UserMessage|null $message
 * @property-read \App\Models\User\User $student
 * @property-read \App\Models\User\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeacherInvitation whereUpdatedAt($value)
 * @mixin \Eloquent
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
