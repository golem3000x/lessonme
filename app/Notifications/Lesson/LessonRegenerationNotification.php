<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonRegenerationStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonRegenerationNotification extends AbstractLessonNotification
{
    public function __construct(
        protected User $user,
        protected LessonRegenerationStatusEnum $status,
        protected ?int $entity_id,
    ) {
        parent::__construct($user, $entity_id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return $this->status;
    }

    protected function getEventType(): BackedEnum
    {
        return NotificationEventTypeEnum::REGENERATION;
    }
}
