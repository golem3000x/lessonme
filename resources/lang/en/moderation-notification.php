<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'Your content has been approved 🎉',
    ModerationStatusEnum::REJECTED->name => 'Your content was rejected ❌',
    ModerationStatusEnum::INVALID->name => 'Your content failed moderation ⚠️',
];
