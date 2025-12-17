<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\System\UserRoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

/**
 * Trait for managing user roles.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 */
trait HasRoles
{
    /**
     * Get all roles assigned to this user.
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            \App\Enums\System\TableNameEnum::ROLE_USER->value
        )->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(UserRoleEnum $role): bool
    {
        return $this->roles()->where('name', '=', $role->value)->exists();
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRoleEnum::ADMIN);
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->hasRole(UserRoleEnum::TEACHER);
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole(UserRoleEnum::STUDENT);
    }

    /**
     * Assign a role to the user.
     *
     * @param  UserRoleEnum  $role_enum  Role to assign
     * @param  User|null  $actor  User performing the operation (for audit)
     */
    public function assignRole(UserRoleEnum $role_enum, ?User $actor = null): void
    {
        $role = Role::findByEnum($role_enum);

        if (! $role) {
            Log::warning('Role not found', ['role' => $role_enum->value]);

            return;
        }

        if ($this->roles()->where('role_id', '=', $role->id)->exists()) {
            return;
        }

        if ($actor) {
            Log::info('User role assigned', [
                'user_id' => $this->id,
                'user_email' => $this->email,
                'role' => $role_enum->value,
                'assigned_by' => $actor->id,
                'assigned_by_email' => $actor->email,
            ]);
        }

        $this->roles()->attach($role->id);
    }

    /**
     * Remove a role from the user.
     *
     * @param  UserRoleEnum  $role_enum  Role to remove
     * @param  User|null  $actor  User performing the operation (for audit)
     */
    public function removeRole(UserRoleEnum $role_enum, ?User $actor = null): void
    {
        $role = Role::findByEnum($role_enum);

        if (! $role) {
            return;
        }

        if ($actor) {
            Log::info('User role removed', [
                'user_id' => $this->id,
                'user_email' => $this->email,
                'role' => $role_enum->value,
                'removed_by' => $actor->id,
                'removed_by_email' => $actor->email,
            ]);
        }

        $this->roles()->detach($role->id);
    }

    /**
     * Sync user roles.
     *
     * @param  array<UserRoleEnum>  $role_enums  Roles to sync
     */
    public function syncRoles(array $role_enums): void
    {
        $role_ids = [];

        foreach ($role_enums as $role_enum) {
            $role = Role::findByEnum($role_enum);
            if ($role) {
                $role_ids[] = $role->id;
            }
        }

        $this->roles()->sync($role_ids);
    }
}
