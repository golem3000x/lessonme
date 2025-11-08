<?php

declare(strict_types=1);

namespace App\Notifications\Translate;

use App\Enums\NotificationEventTypeEnum;
use App\Enums\TranslateStatusEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Notifications\Abstracts\AbstractSlugEntityNotification;

class SlugEntityTranslateNotification extends AbstractSlugEntityNotification
{
    public function __construct(
        SlugEntity $entity,
        private readonly TranslateStatusEnum $translate_status_enum
    ) {
        parent::__construct($entity);
    }

    protected function getStatusEnum(): \BackedEnum
    {
        return $this->translate_status_enum;
    }

    protected function getEventType(): string
    {
        return NotificationEventTypeEnum::TRANSLATE->value;
    }
}
