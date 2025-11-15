<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Lesson\Lesson;
use Exception;

class LessonExistsException extends Exception
{
    public Lesson $lesson;

    public function __construct(Lesson $lesson, string $message = 'Similar lesson already exists.')
    {
        parent::__construct($message);
        $this->lesson = $lesson;
    }
}
