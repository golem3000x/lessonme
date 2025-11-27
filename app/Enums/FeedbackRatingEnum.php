<?php

declare(strict_types=1);

namespace App\Enums;

enum FeedbackRatingEnum: string
{
    case LIKE = 'like';
    case DISLIKE = 'dislike';
}
