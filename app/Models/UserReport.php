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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $reporter
 * @property-read User $reportedUser
 * @property-read User|null $resolvedBy
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
