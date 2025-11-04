<?php

declare(strict_types=1);

use App\Enums\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'Dịch thành công ✅',
    TranslateStatusEnum::INVALID->name => 'Dịch thất bại ⚠️',
];
