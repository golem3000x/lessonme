<?php

declare(strict_types=1);

use App\Enums\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'PHP',
    ModerationStatusEnum::REJECTED->name => 'JavaScript',
    ModerationStatusEnum::INVALID->name => 'Python',
];
