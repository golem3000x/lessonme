<?php

declare(strict_types=1);

namespace App\Services\OpenAi;

use App\Dto\LessonDto;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    const string GPT_FOUR_O_MINI = 'gpt-4o-mini';

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
}
