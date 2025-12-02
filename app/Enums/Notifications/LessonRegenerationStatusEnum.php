<?php

declare(strict_types=1);

namespace App\Enums\Notifications;

enum LessonRegenerationStatusEnum: string
{
    case SUCCESSFULLY = 'successfully';
    case PROCESSING = 'processing';
    case EXISTS = 'exists';
    case ERROR = 'error';
}
