<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'ဘာသာပြန်မှု အောင်မြင်စွာပြီးဆုံးပါပြီ ✅',
    TranslateStatusEnum::INVALID->name => 'ဘာသာပြန်မှု မအောင်မြင်ပါ ⚠️',
];
