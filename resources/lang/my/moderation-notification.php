<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'သင့်အကြောင်းအရာကို အတည်ပြုပြီးပါပြီ 🎉',
    ModerationStatusEnum::REJECTED->name => 'သင့်အကြောင်းအရာကို ပယ်ချခဲ့သည် ❌',
    ModerationStatusEnum::INVALID->name => 'သင့်အကြောင်းအရာ စစ်ဆေးမှုမအောင်မြင်ပါ ⚠️',
];
