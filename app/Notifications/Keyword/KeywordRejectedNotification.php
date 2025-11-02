<?php

declare(strict_types=1);

namespace App\Notifications\Keyword;

use App\Models\Keyword\Keyword;
use App\Notifications\Abstracts\SlugEntityRejectedNotification;

class KeywordRejectedNotification extends SlugEntityRejectedNotification
{
    public function __construct(public Keyword $keyword)
    {
        parent::__construct($keyword);
    }

    protected function getEntityName(): string
    {
        return 'keyword';
    }

    protected function getEntityIdKey(): string
    {
        return 'keyword_id';
    }
}
