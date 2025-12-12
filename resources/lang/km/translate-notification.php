<?php

declare(strict_types=1);

use App\Enums\Notifications\TranslateStatusEnum;

return [
    TranslateStatusEnum::SUCCESSFULLY->name => 'ការបកប្រែបានបញ្ចប់ដោយជោគជ័យ ✅',
    TranslateStatusEnum::INVALID->name => 'ការបកប្រែបានបរាជ័យ ⚠️',
];
