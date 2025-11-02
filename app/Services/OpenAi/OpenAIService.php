<?php

declare(strict_types=1);

namespace App\Services\OpenAi;

use App\Dto\LessonDto;
use App\Enums\SystemLanguageEnum;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    public const GPT_FOUR_O_MINI = 'gpt-4o-mini';

    public const OMNI_MODERATION_LATEST = 'omni-moderation-latest';

    public function generateLesson(LessonDto $lessonDto): array
    {
        $interest = count($lessonDto->themes) ? implode(', ', $lessonDto->themes) : 'general';

        $learning_lang_name = $lessonDto->learning_lang;

        $native_lang_name = $lessonDto->native_lang;

        $teacher_prompt = "You are an $learning_lang_name teacher.";

        $explanation_prompt = "Generate explanations in $native_lang_name for a student learning $learning_lang_name.";

        $topic = $lessonDto->topic;

        $system = "$teacher_prompt Generate a short lesson for a learner. Return JSON with keys:
                    - topic
                    - lesson_name (not longer than 100 characters)
                    - lesson_text
                    - grammar_highlights (array of {phrase,label,explanation,formula,example})
                    - vocabulary (array of {word,translation,example})
                    - quiz (array of {question,options,answer})
                    Use themes: $interest. $explanation_prompt";

        $response = OpenAI::chat()->create([
            'model' => self::GPT_FOUR_O_MINI,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Create lesson about $topic for level $lessonDto->level."],
            ],
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 800,
        ]);

        $content = $response->choices[0]->message->content ?? '{}';

        return json_decode($content, true) ?? [];
    }

    /**
     * Moderate text using OpenAI moderation API
     */
    public function moderateText(string $text): bool
    {
        $response = OpenAI::moderations()->create([
            'model' => self::OMNI_MODERATION_LATEST,
            'input' => $text,
        ]);

        return ! ($response->results[0]->flagged ?? false);
    }

    /**
     * Translate text to target language using OpenAI
     */
    public function translateText(string $text, SystemLanguageEnum $target_lang): ?string
    {
        $prompt = 'Translate the following text into '.$target_lang->value.', keeping it concise and natural. Return only the translated text:'."\n\n".$text;

        $response = OpenAI::chat()->create([
            'model' => self::GPT_FOUR_O_MINI,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a professional translator.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 40,
        ]);

        $content = trim($response->choices[0]->message->content ?? '');

        return $content !== '' ? $content : null;
    }
}
