<?php

declare(strict_types=1);

namespace App\Enums\System;

enum QueueNameEnum: string
{
    case LESSON = 'lesson';
    case SLUG = 'slug';
}