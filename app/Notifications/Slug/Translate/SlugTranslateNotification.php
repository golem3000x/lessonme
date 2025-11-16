<?php

declare(strict_types=1);

namespace App\Notifications\Slug\Translate;

use App\Enums\NotificationEventTypeEnum;
use App\Enums\TranslateStatusEnum;
use App\Models\Abstracts\Slug\Slug;
use App\Notifications\Abstracts\AbstractSlugNotification;

class SlugTranslateNotification extends AbstractSlugNotification
{
    public function __construct(
        Slug                                 $entity,
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
