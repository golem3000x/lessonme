<?php

declare(strict_types=1);

namespace App\Enums;

// TODO уведомления пока все как полная чушь.
enum EntityTypeEnum: string
{
    case LESSON = 'lesson';
    case TOPIC = 'topic';
    case KEYWORD = 'keyword';
    case USER = 'user';
}
