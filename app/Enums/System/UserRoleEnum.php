<?php

declare(strict_types=1);

namespace App\Enums\System;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this === self::TEACHER;
    }

    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }
}
