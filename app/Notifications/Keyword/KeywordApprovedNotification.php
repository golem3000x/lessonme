<?php

declare(strict_types=1);

namespace App\Notifications\Keyword;

use App\Models\Keyword\Keyword;
use App\Notifications\Abstracts\SlugEntityApprovedNotification;

class KeywordApprovedNotification extends SlugEntityApprovedNotification
{
    public function __construct(public Keyword $keyword)
    {
        parent::__construct($keyword);
    }

    protected function getEntityName(): string
    {
        return 'keyword';
    }

    protected function getEntityUrl(): string
    {
        return '/keywords';
    }

    protected function getEntityIdKey(): string
    {
        return 'keyword_id';
    }
}
