<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Enums\Notifications\NotificationEventTypeEnum;
use App\Models\User;
use App\Notifications\Abstracts\AbstractLessonNotification;
use BackedEnum;

class LessonCreationNotification extends AbstractLessonNotification
{
    public function __construct(
        protected User $user,
        protected LessonCreationStatusEnum $status,
        protected ?int                  $entity_id,
    )
    {
        parent::__construct($user, $entity_id);
    }

    protected function getStatusEnum(): BackedEnum
    {
        return $this->status;
    }

    protected function getEventType(): BackedEnum
    {
        return NotificationEventTypeEnum::CREATION;
    }
}
