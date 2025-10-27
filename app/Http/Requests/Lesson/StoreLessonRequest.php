<?php

declare(strict_types=1);

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'topic_id' => ['required', 'integer', 'exists:topics,topic_id'],
            'level' => ['required', 'string'],
            'themes' => ['nullable', 'array'],
        ];
    }
}
