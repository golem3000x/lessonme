<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'Dịch thành công ✅',
    TranslateStatusEnum::INVALID->name => 'Dịch thất bại ⚠️',
];
