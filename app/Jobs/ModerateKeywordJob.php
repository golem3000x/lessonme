<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Notifications\Keyword\KeywordApprovedNotification;
use App\Notifications\Keyword\KeywordErrorNotification;
use App\Notifications\Keyword\KeywordRejectedNotification;
use Illuminate\Notifications\Notification;

class ModerateKeywordJob extends ModerateSlugEntityJob
{
    public function __construct(public Keyword $keyword)
    {
        parent::__construct($keyword);
    }

    protected function getTranslationModelClass(): string
    {
        return KeywordTranslation::class;
    }

    protected function getEntityIdColumn(): string
    {
        return 'keyword_id';
    }

    protected function getApprovedNotification(): Notification
    {
        return new KeywordApprovedNotification($this->keyword);
    }

    protected function getRejectedNotification(): Notification
    {
        return new KeywordRejectedNotification($this->keyword);
    }

    protected function getErrorNotification(): Notification
    {
        return new KeywordErrorNotification($this->keyword);
    }
}
