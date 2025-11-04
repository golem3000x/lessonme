<?php

declare(strict_types=1);

use App\Enums\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'Nội dung của bạn đã được phê duyệt 🎉',
    ModerationStatusEnum::REJECTED->name => 'Nội dung của bạn đã bị từ chối ❌',
    ModerationStatusEnum::INVALID->name => 'Nội dung của bạn không hợp lệ ⚠️',
];