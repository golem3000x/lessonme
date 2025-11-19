<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\LessonCreationStatusEnum;
use App\Models\Lesson\Lesson;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonCreationNotification extends AbstractLessonNotification
{
    public function __construct(
        protected ?Lesson $entity,
        protected LessonCreationStatusEnum $status
    ) {
        parent::__construct($entity);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return $this->status;
    }

    protected function getEventType(): string
    {
        return 'creation';
    }
}
