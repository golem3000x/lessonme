<?php

declare(strict_types=1);

namespace App\Enums;

enum LessonCreationStatusEnum: string
{
    case SUCCESSFULLY = 'successfully';
    case PROCESSING = 'processing';
    case ERROR = 'error';
}
