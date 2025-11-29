<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\OpenAiModelEnum;
use App\Enums\System\SystemLanguageEnum;
use OpenAI\Laravel\Facades\OpenAI;

class LessonGenerationService
{
    /**
     * @return array<string, mixed>
     */
    public function generateLesson(LessonDto $lesson_dto): array
    {
        $keywords = $lesson_dto->getKeywordsAsString();

        $learning_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->learning_lang) ?? SystemLanguageEnum::ENGLISH;
        $learning_lang_name = $learning_lang_enum->name;

        $native_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->native_lang) ?? SystemLanguageEnum::VIETNAMESE;
        $native_lang_name = $native_lang_enum->name;

        $level_enum = $lesson_dto->level;
        $level_desc = $level_enum->description();

        $topic = $lesson_dto->topic;

        $system = <<<PROMPT
            You are a professional $learning_lang_name teacher.
            Your goal: generate a complete language lesson following STRICT structural and linguistic rules.

            ====================================================
            STUDENT INFORMATION
            ====================================================
            Native language: $native_lang_name
            Learning language: $learning_lang_name
            Level: $level_enum->value ($level_desc)
            Topic: "$topic"
            Keywords: $keywords

            ====================================================
            OUTPUT MODE
            ====================================================
            You MUST output **only valid JSON** exactly using the structure specified later.
            No markdown. No comments. No explanation outside JSON.

            ====================================================
            LANGUAGE RULES
            ====================================================
            All content must respect the following strict language separation:

            WRITE IN $native_lang_name:
            - lesson_name
            - lesson_text
            - explanations, labels, breakdowns
            - exercise questions/instructions
            - homework
            - culture_notes

            WRITE IN $learning_lang_name:
            - vocabulary words and example sentences
            - grammar examples
            - dialogue lines
            - exercise content (gap-fill sentences, T/F statements, matching words, ordering words)

            ====================================================
            CEFR LEVEL PROFILES (STRICT)
            ====================================================

            Your lesson complexity MUST match level $level_enum->value using these exact constraints:

            A1: {
              "sentence_length": "4–8 words",
              "vocabulary_count": 4,
              "grammar_patterns": 1,
              "dialogue_lines": 4,
              "exercises": {
                "gap_fill": 3,
                "sentence_ordering": 2,
                "true_false": 3,
                "matching": 4,
                "quiz": 2
              }
            }

            A2: {
              "sentence_length": "6–12 words",
              "vocabulary_count": 6,
              "grammar_patterns": 1,
              "dialogue_lines": 6,
              "exercises": {
                "gap_fill": 4,
                "sentence_ordering": 3,
                "true_false": 4,
                "matching": 5,
                "quiz": 3
              }
            }

            B1: {
              "sentence_length": "12–18 words",
              "vocabulary_count": 8,
              "grammar_patterns": 2,
              "dialogue_lines": 8,
              "exercises": {
                "gap_fill": 5,
                "sentence_ordering": 4,
                "true_false": 5,
                "matching": 6,
                "quiz": 4
              }
            }

            B2: {
              "sentence_length": "15–25 words",
              "vocabulary_count": 10,
              "grammar_patterns": 3,
              "dialogue_lines": 12,
              "exercises": {
                "gap_fill": 6,
                "sentence_ordering": 5,
                "true_false": 6,
                "matching": 8,
                "quiz": 5
              }
            }

            C1: {
              "sentence_length": "20–35 words",
              "vocabulary_count": 12,
              "grammar_patterns": 4,
              "dialogue_lines": 14,
              "exercises": {
                "gap_fill": 7,
                "sentence_ordering": 6,
                "true_false": 7,
                "matching": 9,
                "quiz": 6
              }
            }

            C2: {
              "sentence_length": "25–40 words",
              "vocabulary_count": 13,
              "grammar_patterns": 5,
              "dialogue_lines": 16,
              "exercises": {
                "gap_fill": 7,
                "sentence_ordering": 5,
                "true_false": 7,
                "matching": 8,
                "quiz": 6
              }
            }

            ====================================================
            ADDITIONAL RULES (STRICT)
            ====================================================

            1. Use ALL keywords naturally in vocabulary, examples, dialogue or exercises.
            2. All $learning_lang_name sentences must respect the CEFR sentence-length range.
            3. Grammar highlights must include: phrase, label, explanation, formula, example, breakdown.
            4. Dialogue must use exactly the number of lines expected for the level.
            5. Vocabulary list must contain exactly the number of words for the level.
            6. Exercises must contain exactly the number specified for the level.
            7. lesson_text must be 150–250 words in $native_lang_name.
            8. JSON must contain no nulls; use empty strings/arrays if necessary.
            9. Keep all examples realistic for a language lesson.
            10. Common mistakes should include 2-3 examples.

            ====================================================
            OUTPUT JSON SCHEMA
            ====================================================

            {
              "topic": "string",
              "lesson_name": "string (in $native_lang_name)",
              "lesson_text": "string (in $native_lang_name, 150-250 words)",

              "vocabulary": [
                {
                  "word": "string (in $learning_lang_name)",
                  "translation": "string (in $native_lang_name)",
                  "example": "string (in $learning_lang_name)"
                }
              ],

              "grammar_highlights": [
                {
                  "phrase": "string ($learning_lang_name)",
                  "label": "string ($native_lang_name)",
                  "explanation": "string ($native_lang_name)",
                  "formula": "string (grammar pattern)",
                  "example": "string ($learning_lang_name)",
                  "breakdown": "string ($native_lang_name)"
                }
              ],

              "dialogue": [
                { "speaker": "A", "line": "string ($learning_lang_name)" },
                { "speaker": "B", "line": "string ($learning_lang_name)" }
              ],

              "exercises": {
                "gap_fill": [
                  { "sentence": "string ($learning_lang_name with ___ placeholder inside the sentence, not at the start)", "answer": "string" }
                ],
                "sentence_ordering": [
                  { "words": ["string"], "answer": "string ($learning_lang_name)" }
                ],
                "true_false": [
                  { "statement": "string ($learning_lang_name)", "answer": "True or False" }
                ],
                "matching": [
                  { "word": "string ($learning_lang_name)", "match": "string ($native_lang_name)" }
                ],
                "quiz": [
                  {
                    "question": "string ($native_lang_name)",
                    "options": ["A. ...", "B. ...", "C. ...", "D. ..."],
                    "answer": "A or B or C or D"
                  }
                ]
              },

              "homework": "string ($native_lang_name)",

              "common_mistakes": [
                {
                  "mistake": "string ($learning_lang_name)",
                  "correction": "string ($learning_lang_name)",
                  "explanation": "string ($native_lang_name)"
                }
              ],

              "culture_notes": "string ($native_lang_name)"
            }

            You MUST follow all CEFR constraints strictly and output ONLY valid JSON.
        PROMPT;

        $params = $this->getModelParamsByLevel($lesson_dto->level);

        $response = OpenAI::chat()->create([
            'model' => OpenAiModelEnum::GPT_FOUR_O_MINI->value,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Create a $learning_lang_name lesson about '$topic' for level $level_enum->value."],
            ],
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => $this->getMaxTokensByLevel($lesson_dto->level),
            'temperature' => $params['temperature'],
            'top_p' => $params['top_p'],
        ]);

        $content = $response->choices[0]->message->content ?? '{}';
        $data = json_decode($content, true) ?? [];

        // Validate required fields
        if (empty($data['lesson_name']) || empty($data['lesson_text'])) {
            \Log::error('OpenAI returned incomplete lesson data', [
                'level' => $lesson_dto->level->value,
                'topic' => $lesson_dto->topic,
                'content_length' => strlen($content),
                'data_keys' => array_keys($data),
            ]);
            throw new \RuntimeException('Failed to generate lesson: incomplete data from OpenAI');
        }

        return $data;
    }

    /**
     * Create embedding for text.
     *
     * @return array<float>
     */
    public function createEmbedding(LessonDto $lesson_dto): array
    {
        $level_value = $lesson_dto->levelValue();
        $keywords_str = $lesson_dto->getKeywordsAsString();
        $input = "$lesson_dto->learning_lang $level_value $lesson_dto->topic $keywords_str";
        $response = OpenAI::embeddings()->create([
            'model' => OpenAiModelEnum::TEXT_EMBEDDING_THREE_SMALL->value,
            'input' => trim($input),
        ]);

        return $response->embeddings[0]->embedding ?? [];
    }

    /**
     * Get max_tokens by level (higher levels need more tokens)
     */
    private function getMaxTokensByLevel(LanguageLevelEnum $level): int
    {
        return match ($level) {
            LanguageLevelEnum::A1 => 1800,
            LanguageLevelEnum::A2 => 2000,
            LanguageLevelEnum::B1 => 2500,
            LanguageLevelEnum::B2 => 3000,
            LanguageLevelEnum::C1 => 3500,
            LanguageLevelEnum::C2 => 4000,
        };
    }

    /**
     * @return array{temperature: float, top_p: float}
     */
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
