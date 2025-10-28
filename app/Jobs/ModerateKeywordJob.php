<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SystemLanguageEnum;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateKeywordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Keyword $keyword) {}

    public function handle(OpenAIService $openAI): void
    {
        $translation = $this->keyword->translation();

        if (! $translation) {
            return;
        }

        $original_value = $translation->value;

        $target_lang = SystemLanguageEnum::tryFrom($this->keyword->user?->learning_lang ?? '');

        $is_approved = $openAI->moderateKeywordValue($original_value);

        if (! $is_approved) {
            // For now, simply skip translation if not approved
            return;
        }

        if ($target_lang === null) {
            return;
        }

        $translated = $openAI->translateKeywordValue($original_value, $target_lang);

        if ($translated === null || $translated === '') {
            return;
        }

        KeywordTranslation::updateOrCreate(
            [
                'keyword_id' => $this->keyword->id,
                'language_code' => $target_lang->value,
            ],
            [
                'value' => $translated,
            ]
        );
    }
}
