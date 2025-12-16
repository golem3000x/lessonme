<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'ເນື້ອຫາຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ 🎉',
    ModerationStatusEnum::REJECTED->name => 'ເນື້ອຫາຂອງທ່ານຖືກປະຕິເສດ ❌',
    ModerationStatusEnum::INVALID->name => 'ເນື້ອຫາຂອງທ່ານບໍ່ຜ່ານການກວດສອບ ⚠️',
];
