<?php

declare(strict_types=1);

namespace App\Enums;

enum EntityTypeEnum: string
{
    case LESSON = 'lesson';
    case TOPIC = 'topic';
    case KEYWORD = 'keyword';
}
