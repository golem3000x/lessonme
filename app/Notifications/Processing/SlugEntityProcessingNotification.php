<?php

declare(strict_types=1);

namespace App\Notifications\Processing;

use App\Enums\ModerationStatusEnum;
use App\Enums\NotificationEventTypeEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Notifications\Abstracts\AbstractSlugEntityNotification;

class SlugEntityProcessingNotification extends AbstractSlugEntityNotification
{
    public function __construct(SlugEntity $entity)
    {
        parent::__construct($entity);
    }

    protected function getStatusEnum(): \BackedEnum
    {
        return ModerationStatusEnum::PENDING;
    }

    protected function getEventType(): string
    {
        return NotificationEventTypeEnum::PROCESSING->value;
    }
}
