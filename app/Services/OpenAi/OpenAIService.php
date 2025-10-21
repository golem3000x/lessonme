<?php
declare(strict_types=1);

namespace App\Services\OpenAi;

use App\Dto\LessonDto;
use App\Enums\AvailableLanguageNameEnum;
use JetBrains\PhpStorm\NoReturn;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    #[NoReturn]
    public function generateLesson(LessonDto $lessonDto): array
    {
        $interest = count($lessonDto->themes) ? implode(', ', $lessonDto->themes) : 'general';

        $learning_lang_name = AvailableLanguageNameEnum::fromKey($lessonDto->learning_lang);

        $native_lang_name = AvailableLanguageNameEnum::fromKey($lessonDto->native_language);

        $teacher_prompt = "You are an $learning_lang_name teacher.";

        $explanation_prompt = "Generate explanations in $native_lang_name for a student learning $learning_lang_name.";

        $system = "$teacher_prompt Generate a short lesson for a learner. Return JSON with keys:
                    - topic
                    - lesson_text
                    - grammar_highlights (array of {phrase,label,explanation,formula,example})
                    - vocabulary (array of {word,translation,example})
                    - quiz (array of {question,options,answer})
                    Use themes: $interest. $explanation_prompt";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Create lesson about $lessonDto->topic for level $lessonDto->level."],
            ],
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 800
        ]);

        $content = $response->choices[0]->message->content ?? '{}';
        return json_decode($content, true) ?? [];
    }
}
