<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\System\TableNameEnum;
use App\Enums\System\UserRoleEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $table = TableNameEnum::ROLES->value;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all users with this role.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TableNameEnum::ROLE_USER->value
        )->withTimestamps();
    }

    /**
     * Find role by enum value.
     */
    public static function findByEnum(UserRoleEnum $role_enum): ?self
    {
        return self::where('name', '=', $role_enum->value)->first();
    }

    /**
     * Check if this is admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === UserRoleEnum::ADMIN->value;
    }

    /**
     * Check if this is teacher role.
     */
    public function isTeacher(): bool
    {
        return $this->name === UserRoleEnum::TEACHER->value;
    }

    /**
     * Check if this is student role.
     */
    public function isStudent(): bool
    {
        return $this->name === UserRoleEnum::STUDENT->value;
    }
}
