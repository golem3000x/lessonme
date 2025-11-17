<?php

declare(strict_types=1);

namespace App\Notifications\Slug\Processing;

use App\Enums\ModerationStatusEnum;
use App\Enums\NotificationEventTypeEnum;
use App\Models\Abstracts\Slug\Slug;
use App\Notifications\Abstracts\AbstractSlugNotification;

class SlugProcessingNotification extends AbstractSlugNotification
{
    public function __construct(Slug $entity)
    {
        parent::__construct($entity);
    }

    protected function getStatusEnum(): \BackedEnum
    {
        return ModerationStatusEnum::PENDING;
    }

    protected function getEventType(): \BackedEnum
    {
        return NotificationEventTypeEnum::PROCESSING;
    }
}
