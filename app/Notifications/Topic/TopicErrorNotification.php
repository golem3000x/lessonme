<?php

declare(strict_types=1);

namespace App\Notifications\Topic;

use App\Models\Topic\Topic;
use App\Notifications\Abstracts\SlugEntityErrorNotification;

class TopicErrorNotification extends SlugEntityErrorNotification
{
    public function __construct(public Topic $topic)
    {
        parent::__construct($topic);
    }

    protected function getEntityName(): string
    {
        return 'topic';
    }

    protected function getEntityIdKey(): string
    {
        return 'topic_id';
    }
}
