<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ModerationStatusEnum;
use App\Enums\SystemLanguageEnum;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Notifications\Topic\TopicApprovedNotification;
use App\Notifications\Topic\TopicErrorNotification;
use App\Notifications\Topic\TopicRejectedNotification;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateTopicJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Topic $topic)
    {
    }

    public function handle(OpenAIService $openAI): void
    {
        $status = $this->processTopic($openAI);

        $this->notifyUser($status);
    }

    private function processTopic(OpenAIService $openAI): ModerationStatusEnum
    {
        $translation = $this->topic->translation();
        $targetLang = SystemLanguageEnum::tryFrom($this->topic->user->learning_lang);

        if (!$translation || !$targetLang) {
            return ModerationStatusEnum::INVALID;
        }

        $originalValue = $translation->value;

        if (!$this->topic->is_approved && !$openAI->moderateTopicName($originalValue)) {
            return ModerationStatusEnum::REJECTED;
        }

        $this->topic->update(['is_approved' => true]);

        $translated = $openAI->translateTopicName($originalValue, $targetLang);

        if (!empty($translated)) {
            TopicTranslation::updateOrCreate(
                [
                    'topic_id' => $this->topic->id,
                    'language_code' => $targetLang->value,
                ],
                [
                    'value' => $translated,
                ]
            );
        } else {
            return ModerationStatusEnum::INVALID;
        }

        return ModerationStatusEnum::APPROVED;
    }

    private function notifyUser(ModerationStatusEnum $status): void
    {
        $user = $this->topic->user;

        match ($status) {
            ModerationStatusEnum::APPROVED =>
            $user->notify(new TopicApprovedNotification($this->topic)),

            ModerationStatusEnum::REJECTED =>
            $user->notify(new TopicRejectedNotification($this->topic)),

            ModerationStatusEnum::INVALID =>
            $user->notify(new TopicErrorNotification($this->topic)),
        };
    }
}
