<?php

declare(strict_types=1);

use App\Enums\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'Translation completed successfully ✅',
    TranslateStatusEnum::INVALID->name => 'Translation failed ⚠️',
];
