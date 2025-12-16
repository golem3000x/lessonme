<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'ການແປສຳເລັດແລ້ວ ✅',
    TranslateStatusEnum::INVALID->name => 'ການແປລົ້ມເຫຼວ ⚠️',
];
