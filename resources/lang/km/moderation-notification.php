<?php

declare(strict_types=1);

use App\Enums\Notifications\ModerationStatusEnum;

return [
    ModerationStatusEnum::APPROVED->name => 'មាតិការបស់អ្នកត្រូវបានអនុម័ត 🎉',
    ModerationStatusEnum::REJECTED->name => 'មាតិការបស់អ្នកត្រូវបានបដិសេធ ❌',
    ModerationStatusEnum::INVALID->name => 'មាតិការបស់អ្នកបានបរាជ័យក្នុងការត្រួតពិនិត្យ ⚠️',
];
