<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SystemLanguageEnum;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Notifications\TopicApprovedNotification;
use App\Notifications\TopicRejectedNotification;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateTopicJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Topic $topic) {}

    public function handle(OpenAIService $openAI): void
    {
        $translation = $this->topic->translation();

        if (! $translation) {
            return;
        }

        $original_name = $translation->value;

        $target_lang = SystemLanguageEnum::tryFrom($this->topic->user->learning_lang);

        $is_approved = $openAI->moderateTopicName($original_name);

        $this->topic->update(['is_approved' => $is_approved]);

        if ($is_approved) {
            $translated = $openAI->translateTopicName($original_name, $target_lang);
            TopicTranslation::updateOrCreate(
                [
                    'topic_id' => $this->topic->id,
                    'language_code' => $target_lang,
                ],
                [
                    'value' => $translated,
                ]
            );

            $this->topic->user->notify(new TopicApprovedNotification($this->topic));
        } else {
            $this->topic->user->notify(new TopicRejectedNotification($this->topic));
        }
    }
}
