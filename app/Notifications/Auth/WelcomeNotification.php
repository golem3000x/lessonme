<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Enums\AuthNotificationStatusEnum;
use App\Models\User;
use App\Notifications\Abstracts\AbstractAuthNotification;
use BackedEnum;

class WelcomeNotification extends AbstractAuthNotification
{
    public function __construct(
        ?User $entity,
        string $message = ''
    ) {
        parent::__construct($entity, $message);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return AuthNotificationStatusEnum::WELCOME;
    }
}
