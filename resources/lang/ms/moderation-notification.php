<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'Kandungan anda telah diluluskan 🎉',
    ModerationStatusEnum::REJECTED->name => 'Kandungan anda telah ditolak ❌',
    ModerationStatusEnum::INVALID->name => 'Kandungan anda gagal moderasi ⚠️',
];
