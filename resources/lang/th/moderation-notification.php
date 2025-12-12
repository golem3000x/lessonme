<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'เนื้อหาของคุณได้รับการอนุมัติ 🎉',
    ModerationStatusEnum::REJECTED->name => 'เนื้อหาของคุณถูกปฏิเสธ ❌',
    ModerationStatusEnum::INVALID->name => 'เนื้อหาของคุณไม่ผ่านการตรวจสอบ ⚠️',
];
