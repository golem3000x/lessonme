<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Enums\LanguageLevelEnum;
use App\Enums\OpenAiModelEnum;
use App\Enums\SystemLanguageEnum;
use OpenAI\Laravel\Facades\OpenAI;

class LessonGenerationService
{
    /**
     * @return array<string, mixed>
     */
    public function generateLesson(LessonDto $lesson_dto): array
    {
        $keywords = $lesson_dto->getKeywordsAsString();

        $learning_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->learning_lang);
        $learning_lang_name = $learning_lang_enum->name ?? SystemLanguageEnum::ENGLISH;

        $native_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->native_lang);
        $native_lang_name = $native_lang_enum->name ?? SystemLanguageEnum::VIETNAMESE;

        $level_enum = $lesson_dto->level ?? LanguageLevelEnum::A1;
        $level_desc = $level_enum->description();

        $topic = $lesson_dto->topic;

        $system = <<<PROMPT
            You are a professional $learning_lang_name teacher who creates personalized lessons for foreign learners.

            Student information:
            - Native language: $native_lang_name
            - Learning language: $learning_lang_name
            - Level: $level_enum->value ($level_desc)

            Guidelines:
            1. Keep all explanations in $native_lang_name.
            2. Write examples and exercises in $learning_lang_name.
            3. The lesson must match the student's level:
            - For A1–A2: use short, simple sentences and basic grammar.
            - For B1–B2: use natural language and add light idioms or expressions.
            - For C1–C2: use rich, fluent, and authentic expressions.
            4. Focus strictly on the topic: "$topic".
            5. Include and explain all provided keywords: $keywords.
            6. Be concise and educational. Avoid repetition or filler content.
            7. Return your response **strictly as valid JSON** with this structure:

            {
              "topic": "string",
              "lesson_name": "string (max 100 characters)",
              "lesson_text": "string (short, under 300 words, in $learning_lang_name)",
              "grammar_highlights": [
                {
                  "phrase": "string",
                  "label": "string",
                  "explanation": "string (in $native_lang_name)",
                  "formula": "string (optional)",
                  "example": "string (in $learning_lang_name)"
                }
              ],
              "vocabulary": [
                {
                  "word": "string",
                  "translation": "string (in $native_lang_name)",
                  "example": "string (in $learning_lang_name)"
                }
              ],
              "quiz": [
                {
                  "question": "string (in $learning_lang_name)",
                  "options": ["A", "B", "C", "D"],
                  "answer": "string (correct option)"
                }
              ]
            }
            
            Rules:
            - Output must be **pure JSON**, no markdown or extra text.
            - Do not include explanations or formatting outside JSON.
            - Use all provided keywords naturally in examples or explanations.
        PROMPT;

        $params = $this->getModelParamsByLevel($lesson_dto->level);

        $response = OpenAI::chat()->create([
            'model' => OpenAiModelEnum::GPT_FOUR_O_MINI->value,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Create a $learning_lang_name lesson about '$topic' for level $level_enum->value."],
            ],
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 900,
            'temperature' => $params['temperature'],
            'top_p' => $params['top_p'],
        ]);

        $content = $response->choices[0]->message->content ?? '{}';

        return json_decode($content, true) ?? [];
    }

    /**
     * Создаёт embedding для текста.
     *
     * @return array<float>
     */
    public function createEmbedding(LessonDto $lesson_dto): array
    {
        $level_value = $lesson_dto->levelValue();
        $keywords_str = $lesson_dto->getKeywordsAsString();
        $input = "$level_value $lesson_dto->topic $keywords_str";
        $response = OpenAI::embeddings()->create([
            'model' => OpenAiModelEnum::TEXT_EMBEDDING_THREE_SMALL->value,
            'input' => trim($input),
        ]);

        return $response->embeddings[0]->embedding ?? [];
    }

    private function getModelParamsByLevel(LanguageLevelEnum $level): array
    {
        return match ($level) {
            LanguageLevelEnum::A2 => ['temperature' => 0.5, 'top_p' => 0.85],
            LanguageLevelEnum::B1 => ['temperature' => 0.6, 'top_p' => 0.9],
            LanguageLevelEnum::B2 => ['temperature' => 0.7, 'top_p' => 0.9],
            LanguageLevelEnum::C1, LanguageLevelEnum::C2 => ['temperature' => 0.8, 'top_p' => 0.95],
            default => ['temperature' => 0.4, 'top_p' => 0.8],
        };
    }
}
