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

        $learning_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->learning_lang);
        $learning_lang_name = $learning_lang_enum->name ?? SystemLanguageEnum::ENGLISH;

        $native_lang_enum = SystemLanguageEnum::tryFrom($lesson_dto->native_lang);
        $native_lang_name = $native_lang_enum->name ?? SystemLanguageEnum::VIETNAMESE;

        $level_enum = $lesson_dto->level ?? LanguageLevelEnum::A1;
        $level_desc = $level_enum->description();

        $topic = $lesson_dto->topic;

        $system = <<<PROMPT
            You are a professional $learning_lang_name teacher who creates structured, level-appropriate language lessons for foreign learners.

            Student information:
            - Native language: $native_lang_name
            - Learning language: $learning_lang_name
            - Level: $level_enum->value ($level_desc)

            ⚠️ CRITICAL LANGUAGE REQUIREMENTS:
            The student is learning $learning_lang_name but may not understand it well yet.
            They need to understand what the lesson is about and what they need to do.
            
            YOU MUST WRITE IN $native_lang_name:
            - lesson_name (the title explaining what this lesson teaches)
            - lesson_text (introduction explaining the topic and what student will learn)
            - ALL explanations, translations, and instructions
            - ALL exercise questions (so student understands what to do)
            - homework instructions
            - All "label", "explanation", "breakdown" fields
            
            YOU MUST WRITE IN $learning_lang_name:
            - vocabulary words, example sentences
            - dialogue lines
            - grammar phrases and examples
            - exercise content (sentences, options, words to arrange)

            Guidelines:
            1. lesson_name: Write in $native_lang_name explaining what the student will learn.
               Example: "Giới thiệu về JavaScript và Laravel" (NOT "Introduction to JavaScript and Laravel")
            2. lesson_text: Write in $native_lang_name explaining the topic, why it's useful, what they'll learn.
               This helps beginners understand the lesson before starting.
            3. Exercise questions: MUST be in $native_lang_name so student knows what to do.
               Example: "Laravel là gì?" (NOT "What is Laravel?")
            4. Exercise options/content: Must be in $learning_lang_name (this is what they practice).
            5. All explanations, instructions, homework: MUST be in $native_lang_name.
            6. The difficulty must match the CEFR level:
               - A1–A2: short, simple sentences; very basic vocabulary.
               - B1–B2: natural language; moderate complexity; light idioms allowed.
               - C1–C2: fluent, rich, authentic expressions; advanced grammar.
            7. Strictly follow the topic: "$topic".
            8. You MUST naturally include and use all provided keywords: $keywords.
            9. Be concise; avoid repetition.
            10. Every grammar_highlight MUST include:
               - phrase
                - label
                - formula (explicit grammar pattern such as "Subject + Verb + Object")
                - breakdown (explain each element in $native_lang_name)
                - example
            11. When using a grammar pattern inside lesson_text, briefly show the pattern in parentheses.


            Output format:
            Return your answer STRICTLY as valid JSON (no markdown, no comments, no extra text) using this structure:

            {
              "topic": "string",
              "lesson_name": "MUST BE IN $native_lang_name - title explaining the lesson topic",
              "lesson_text": "MUST BE IN $native_lang_name - 150-250 words introduction explaining what student will learn and why it's useful",

              "vocabulary": [
                {
                  "word": "in $learning_lang_name (the word being taught)",
                  "translation": "in $native_lang_name (meaning)",
                  "example": "in $learning_lang_name (sentence using the word)"
                }
              ],

              "grammar_highlights": [
                {
                  "phrase": "in $learning_lang_name (the grammar pattern)",
                  "label": "in $native_lang_name (name of the pattern)",
                  "explanation": "in $native_lang_name (how it works)",
                  "formula": "grammar structure like: Subject + Verb + Object",
                  "example": "in $learning_lang_name (example sentence)",
                  "breakdown": "in $native_lang_name (explain each part simply)"
                }
              ],

              "dialogue": [
                { "speaker": "A", "line": "in $learning_lang_name" },
                { "speaker": "B", "line": "in $learning_lang_name" }
              ],

              "exercises": {
                "gap_fill": [
                  {
                    "sentence": "in $learning_lang_name with ___",
                    "answer": "the missing word"
                  }
                ],
                "sentence_ordering": [
                  {
                    "words": ["in", "$learning_lang_name", "words"],
                    "answer": "correct sentence"
                  }
                ],
                "true_false": [
                  {
                    "statement": "in $learning_lang_name (statement to evaluate)",
                    "answer": "True or False"
                  }
                ],
                "matching": [
                  {
                    "word": "in $learning_lang_name (word to match)",
                    "match": "in $native_lang_name (its meaning)"
                  }
                ],
                "quiz": [
                  {
                    "question": "MUST BE IN $native_lang_name (so student understands the question)",
                    "options": ["A. in $learning_lang_name", "B. in $learning_lang_name", "C. ...", "D. ..."],
                    "answer": "A or B or C or D"
                  }
                ]
              },

              "homework": "MUST BE IN $native_lang_name - clear instruction what to practice",

              "common_mistakes": [
                {
                  "mistake": "in $learning_lang_name (wrong example)",
                  "correction": "in $learning_lang_name (correct version)",
                  "explanation": "in $native_lang_name (why it's wrong and how to fix)"
                }
              ],

              "culture_notes": "MUST BE IN $native_lang_name - cultural context information"
            }

            Rules:
            - Output MUST be pure JSON.
            - No markdown, no comments, no explanations outside JSON.
            - Use ALL keywords naturally.
            - Keep examples and exercises short and level-appropriate.
            - If any field cannot be generated, return an empty array or empty string for that field.
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
