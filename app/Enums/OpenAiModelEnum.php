<?php

declare(strict_types=1);

namespace App\Enums;

enum OpenAiModelEnum: string
{
    case GPT_FOUR_O_MINI = 'gpt-4o-mini';
    case OMNI_MODERATION_LATEST = 'omni-moderation-latest';
    case TEXT_EMBEDDING_THREE_SMALL = 'text-embedding-3-small';
}
