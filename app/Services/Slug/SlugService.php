<?php

declare(strict_types=1);

namespace App\Services\Slug;

use App\Enums\System\OpenAiModelEnum;
use App\Enums\System\SystemLanguageEnum;
use OpenAI\Laravel\Facades\OpenAI;

class SlugService
{
    // Stricter thresholds for educational content (lower = more strict)
    private const STRICT_THRESHOLD = 0.3;
    private const STRICT_CATEGORIES = [
        'sexual',
        'sexual/minors',
        'violence',
        'violence/graphic',
        'hate',
        'harassment',
    ];

    /**
     * Moderate slug using OpenAI moderation API with stricter thresholds
     */
    public function moderateSlug(string $slug): bool
    {
        try {
            $response = OpenAI::moderations()->create([
                'model' => OpenAiModelEnum::OMNI_MODERATION_LATEST->value,
                'input' => $slug,
            ]);

            $result = $response->results[0];
            $flagged = $result->flagged ?? false;
            $categories = $result->categories ?? [];
            $category_scores = $result->categoryScores ?? null;
            
            // Check with stricter thresholds
            $strict_flagged = false;
            $triggered_categories = [];
            
            if ($category_scores) {
                foreach (self::STRICT_CATEGORIES as $category) {
                    $score_key = str_replace('/', '', ucwords($category, '/'));
                    $score_key = lcfirst($score_key);
                    
                    $score = $category_scores->$score_key ?? 0;
                    
                    if ($score >= self::STRICT_THRESHOLD) {
                        $strict_flagged = true;
                        $triggered_categories[$category] = $score;
                    }
                }
            }
            
            \Log::info('OpenAI moderation result', [
                'content' => $slug,
                'flagged' => $flagged,
                'strict_flagged' => $strict_flagged,
                'triggered_categories' => $triggered_categories,
                'categories' => $categories
            ]);

            // Reject if either OpenAI flags it OR our strict threshold is exceeded
            return !$flagged && !$strict_flagged;
        } catch (\Exception $e) {
            \Log::error('OpenAI moderation failed - rejecting content by default', [
                'content' => $slug,
                'error' => $e->getMessage()
            ]);
            
            // Reject by default if API fails (conservative approach)
            return false;
        }
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
