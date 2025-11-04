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

    public function generateLesson(LessonDto $lesson_dto): array
    {
        $interest = count($lesson_dto->themes) ? implode(', ', $lesson_dto->themes) : 'general';

        $learning_lang_name = $lesson_dto->learning_lang;

        $native_lang_name = $lesson_dto->native_lang;

        $teacher_prompt = "You are an $learning_lang_name teacher.";

        $explanation_prompt = "Generate explanations in $native_lang_name for a student learning $learning_lang_name.";

        $topic = $lesson_dto->topic;

        $system = <<<PROMPT
                    $teacher_prompt Generate a short lesson for a learner. Return JSON with keys:
                    - topic
                    - lesson_name (not longer than 100 characters)
                    - lesson_text
                    - grammar_highlights (array of {phrase,label,explanation,formula,example})
                    - vocabulary (array of {word,translation,example})
                    - quiz (array of {question,options,answer})
                    Use themes: $interest. $explanation_prompt"
                    PROMPT;

        $response = OpenAI::chat()
            ->create([
                'model' => self::GPT_FOUR_O_MINI,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => "Create lesson about $topic for level $lesson_dto->level."],
                ],
                'response_format' => ['type' => 'json_object'],
                'max_tokens' => 800,
            ]);

        $content = $response->choices[0]->message->content ?? '{}';

        return json_decode($content, true) ?? [];
    }

    /**
     * Moderate slug using OpenAI moderation API
     */
    public function moderateSlug(string $slug): bool
    {
        $response = OpenAI::moderations()->create([
            'model' => self::OMNI_MODERATION_LATEST,
            'input' => $slug,
        ]);

        return ! ($response->results[0]->flagged ?? false);
    }

    /**
     * Translate slug to all language using OpenAI
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
                'model' => self::GPT_FOUR_O_MINI,
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
