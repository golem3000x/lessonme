<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lesson;

use App\Dto\LessonDto;
use App\Http\Controllers\Controller;
use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\User;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Factory;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        protected OpenAIService $open_ai_service
    ) {}

    public function store(Request $request): Redirector|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'topic' => 'nullable|string',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'level' => 'required|string',
            'themes' => 'nullable|array',
        ]);

        $topic_id = $validated['topic_id'] ?? null;
        $topic_label = $validated['topic'] ?? 'General';

        // If topic_id not provided, try to resolve from topic label
        if (! $topic_id && isset($validated['topic'])) {
            $topic = Topic::with('translations')
                ->whereHas('translations', function ($query) use ($validated): void {
                    $query->where('value', $validated['topic']);
                })
                ->first();

            $topic_id = $topic?->id;
            $topic_label = $topic?->translatedValue() ?? $validated['topic'];
        }

        $lesson_dto = new LessonDto(
            topic: $topic_label,
            level: $validated['level'],
            native_lang: $user->native_lang,
            learning_lang: $user->learning_lang,
            keywords: $validated['themes'] ?? []
        );

        $lesson_data = $this->open_ai_service->generateLesson($lesson_dto);

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'topic_id' => $topic_id,
            'lesson_name' => $lesson_data['lesson_name'] ?? 'Lesson',
            'lesson_text' => $lesson_data['lesson_text'] ?? '',
            'grammar_data' => $lesson_data['grammar_highlights'] ?? [],
            'vocabulary' => $lesson_data['vocabulary'] ?? [],
            'quiz' => $lesson_data['quiz'] ?? [],
        ]);

        return redirect()->route('lessons.show', $lesson->id);
    }

    public function show(Lesson $lesson): Factory|View
    {
        abort_if($lesson->user_id !== Auth::id(), 403);

        return view('lessons.show', compact('lesson'));
    }
}
