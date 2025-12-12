<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'การแปลสำเร็จ ✅',
    TranslateStatusEnum::INVALID->name => 'การแปลล้มเหลว ⚠️',
];
