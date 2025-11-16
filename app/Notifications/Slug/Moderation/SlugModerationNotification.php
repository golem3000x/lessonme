<?php

declare(strict_types=1);

namespace App\Notifications\Slug\Moderation;

use App\Enums\ModerationStatusEnum;
use App\Enums\NotificationEventTypeEnum;
use App\Models\Abstracts\Slug\Slug;
use App\Notifications\Abstracts\AbstractSlugNotification;

class SlugModerationNotification extends AbstractSlugNotification
{
    public function __construct(
        Slug                         $entity,
        private ModerationStatusEnum $moderation_status_enum
    ) {
        parent::__construct($entity);
    }

    protected function getStatusEnum(): \BackedEnum
    {
        return $this->moderation_status_enum;
    }

    protected function getEventType(): string
    {
        return NotificationEventTypeEnum::MODERATION->value;
    }
}
