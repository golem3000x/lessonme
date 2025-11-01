<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ModerationStatusEnum;
use App\Enums\SystemLanguageEnum;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Notifications\Keyword\KeywordApprovedNotification;
use App\Notifications\Keyword\KeywordErrorNotification;
use App\Notifications\Keyword\KeywordRejectedNotification;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateKeywordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Keyword $keyword)
    {
    }

    public function handle(OpenAIService $openAI): void
    {
        $status = $this->processKeyword($openAI);

        $this->notifyUser($status);
    }

    private function processKeyword(OpenAIService $openAI): ModerationStatusEnum
    {
        $translation = $this->keyword->translation();
        $targetLang = SystemLanguageEnum::tryFrom($this->keyword->user?->learning_lang ?? '');

        if (!$translation || !$targetLang) {
            return ModerationStatusEnum::INVALID;
        }

        $originalValue = $translation->value;

        if (!$this->keyword->is_approved && !$openAI->moderateKeywordValue($originalValue)) {
            return ModerationStatusEnum::REJECTED;
        }

        $this->keyword->update(['is_approved' => true]);

        $translated = $openAI->translateKeywordValue($originalValue, $targetLang);

        if (!empty($translated)) {
            KeywordTranslation::updateOrCreate(
                [
                    'keyword_id' => $this->keyword->id,
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
        $user = $this->keyword->user;

        match ($status) {
            ModerationStatusEnum::APPROVED =>
            $user->notify(new KeywordApprovedNotification($this->keyword)),

            ModerationStatusEnum::REJECTED =>
            $user->notify(new KeywordRejectedNotification($this->keyword)),

            ModerationStatusEnum::INVALID =>
            $user->notify(new KeywordErrorNotification($this->keyword)),
        };
    }
}
