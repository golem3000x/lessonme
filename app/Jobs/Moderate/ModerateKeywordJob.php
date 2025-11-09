<?php

declare(strict_types=1);

namespace App\Jobs\Moderate;

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

    public function __construct(
        public Keyword $keyword
    ) {}

    public function handle(OpenAIService $open_ai_service): void
    {
        $keyword = $this->keyword->load(['translations', 'user']);

        $first_translation = $keyword->translations->first();

        if (! $first_translation) {
            return;
        }

        $original_value = $first_translation->value;

        // Moderate the keyword using OpenAI
        $is_appropriate = $open_ai_service->moderateSlug($original_value);

        if (! $is_appropriate) {
            // If not appropriate, mark as not approved
            $keyword->update(['is_approved' => false]);

            return;
        }

        // Approve the keyword
        $keyword->update(['is_approved' => true]);

        // Get target language (user's learning language)
        if (! $keyword->user) {
            return;
        }

        $learning_lang = $keyword->user->learning_lang;

        // Translate to all languages
        $translations = $open_ai_service->translateSlugAllLanguages($original_value);

        // Create translation for learning language if it doesn't exist
        if (isset($translations[$learning_lang])) {
            KeywordTranslation::updateOrCreate(
                [
                    'keyword_id' => $keyword->id,
                    'language_code' => $learning_lang,
                ],
                [
                    'value' => $translations[$learning_lang],
                ]
            );
        }
    }
}
