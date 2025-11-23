<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\System\OpenAiModelEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class IsMeaningful implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('validation.meaningful')->translate();

            return;
        }

        // First: RegExp filters (fast local check)
        if (! $this->passesRegExpFilters($value)) {
            $fail('validation.meaningful')->translate();

            return;
        }

        // Second: OpenAI validation (more intelligent check)
        if (! $this->passesOpenAIValidation($value)) {
            $fail('validation.meaningful')->translate();
        }
    }

    /**
     * Check if value passes RegExp filters
     */
    private function passesRegExpFilters(string $value): bool
    {
        $trimmed = trim($value);

        // Must not be empty
        if ($trimmed === '') {
            return false;
        }

        // Must have at least one word with 3+ letters
        if (! preg_match('/[A-Za-zÀ-ÿА-Яа-яЁё]{3,}/u', $trimmed)) {
            return false;
        }

        // Must not consist only of special characters
        if (! preg_match('/^(?![^A-Za-zÀ-ÿА-Яа-яЁё]+$).+/u', $trimmed)) {
            return false;
        }

        // Must have at least 2 letters in a row
        if (! preg_match('/[A-Za-zÀ-ÿА-Яа-яЁё]{2,}/u', $trimmed)) {
            return false;
        }

        return true;
    }

    /**
     * Validate using OpenAI
     */
    private function passesOpenAIValidation(string $value): bool
    {
        try {
            $system_prompt = <<<'PROMPT'
You are a validation system for educational content.
Determine if the provided text is a meaningful topic or keyword for language learning.

Rules:
- Valid: Real topics, subjects, concepts, activities, objects, places, verbs, adjectives, etc.
- Invalid: Random characters, nonsense, gibberish, single letters, pure numbers, spam, profanity

Respond ONLY with valid JSON in this exact format:
{"result":"valid"} or {"result":"invalid"}

No explanation, no additional text, only JSON.
PROMPT;

            $response = OpenAI::chat()->create([
                'model' => OpenAiModelEnum::GPT_FOUR_O_MINI->value,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user', 'content' => $value],
                ],
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'max_tokens' => 50,
            ]);

            $content = $response->choices[0]->message->content ?? '{}';
            $json = json_decode($content, true);

            return isset($json['result']) && $json['result'] === 'valid';
        } catch (\Exception $e) {
            // Log error but don't fail validation
            Log::error('OpenAI validation failed', [
                'error' => $e->getMessage(),
                'value' => $value,
            ]);

            // If OpenAI fails, pass validation (RegExp already checked)
            return true;
        }
    }
}
