<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Notifications\Topic\TopicApprovedNotification;
use App\Notifications\Topic\TopicErrorNotification;
use App\Notifications\Topic\TopicRejectedNotification;
use Illuminate\Notifications\Notification;

class ModerateTopicJob extends ModerateSlugEntityJob
{
    public function __construct(public Topic $topic)
    {
        parent::__construct($topic);
    }

    protected function getTranslationModelClass(): string
    {
        return TopicTranslation::class;
    }

    protected function getEntityIdColumn(): string
    {
        return 'topic_id';
    }

    protected function getApprovedNotification(): Notification
    {
        return new TopicApprovedNotification($this->topic);
    }

    protected function getRejectedNotification(): Notification
    {
        return new TopicRejectedNotification($this->topic);
    }

    protected function getErrorNotification(): Notification
    {
        return new TopicErrorNotification($this->topic);
    }
}
