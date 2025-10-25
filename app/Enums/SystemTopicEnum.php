<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemTopicEnum: string
{
    case PROGRAMMING = 'Programming';
    case COOKING = 'Cooking';
    case TRAVEL = 'Travel';
    case ART = 'Art';
}
