<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\ReportReasonEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $reporter_id
 * @property int $reported_user_id
 * @property ReportReasonEnum $reason
 * @property string|null $description
 * @property bool $is_resolved
 * @property Carbon|null $resolved_at
 * @property int|null $resolved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User $reportedUser
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User|null $resolvedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereIsResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereReportedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserReport extends Model
{
    protected $table = TableNameEnum::USER_REPORTS->value;

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reason',
        'description',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'reason' => ReportReasonEnum::class,
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
