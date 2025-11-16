<?php

declare(strict_types=1);

namespace App\Enums;

enum AuthNotificationStatusEnum: string
{
    case WELCOME = 'welcome';
    case REGISTERED = 'registered';
}
