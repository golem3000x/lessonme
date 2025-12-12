<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'Terjemahan berjaya diselesaikan ✅',
    TranslateStatusEnum::INVALID->name => 'Terjemahan gagal ⚠️',
];
