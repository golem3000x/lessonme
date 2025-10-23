<?php

declare(strict_types=1);

namespace App\Enums;

enum AvailableTopicEnum: int
{
    case Programming = 1;
    case Cooking = 2;
    case Travel = 3;
    case Art = 4;
}
