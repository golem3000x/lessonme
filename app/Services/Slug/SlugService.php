<?php

declare(strict_types=1);

namespace App\Services\Slug;

use App\Enums\OpenAiModelEnum;
use App\Enums\SystemLanguageEnum;
use OpenAI\Laravel\Facades\OpenAI;

class SlugService
{
    /**
     * Moderate slug using OpenAI moderation API
     */
    public function moderateSlug(string $slug): bool
    {
        $response = OpenAI::moderations()->create([
            'model' => OpenAiModelEnum::OMNI_MODERATION_LATEST->value,
            'input' => $slug,
        ]);

        return ! ($response->results[0]->flagged ?? false);
    }

    /**
     * Translate slug to all language using OpenAI
     *
     * @return array<string, string>
     */
    public function translateSlugAllLanguages(string $slug): array
    {
        $languages = implode(',', SystemLanguageEnum::values());

        $system = <<<PROMPT
            You are a professional translator.
            Translate the provided text into all of the following languages: $languages.
            Return only a valid JSON object using ISO language codes as keys.
            Example format:
                {
                    "vi": "Vietnamese translation",
                    "en": "English translation"
                }
            Do not include any explanation or formatting other than JSON."
            PROMPT;

        $response = OpenAI::chat()
            ->create([
                'model' => OpenAiModelEnum::GPT_FOUR_O_MINI->value,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $slug],
                ],
                'response_format' => ['type' => 'json_object'],
                'max_tokens' => 500,
            ]);

        $content = $response->choices[0]->message->content ?? '{}';

        $translations = json_decode($content, true);

        return is_array($translations) ? $translations : [];
    }
}
