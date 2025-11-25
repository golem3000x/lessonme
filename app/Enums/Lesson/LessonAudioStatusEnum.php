<?php

declare(strict_types=1);

namespace App\Enums\Lesson;

enum LessonAudioStatusEnum: string
{
    case PENDING = 'pending';
    case READY = 'ready';
    case FAILED = 'failed';
}
