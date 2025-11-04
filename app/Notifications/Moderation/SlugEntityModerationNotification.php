<?php

declare(strict_types=1);

namespace App\Notifications\Moderation;

use App\Enums\ModerationStatusEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Notifications\Abstracts\AbstractSlugEntityNotification;

class SlugEntityModerationNotification extends AbstractSlugEntityNotification
{
    public function __construct(
        SlugEntity $entity,
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
        return 'moderation';
    }
}
