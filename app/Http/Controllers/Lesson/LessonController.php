<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lesson;

use App\Dto\LessonDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\User;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Factory;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        private readonly OpenAIService $openAIService,
    ) {}

    public function index(): Factory|View
    {
        $lessons = Lesson::where('user_id', Auth::id())->latest()->get();

        return view('lessons.index', compact('lessons'));
    }

    public function create(): Factory|View
    {
        return view('lessons.create');
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = Auth::user();

        $lessonDto = new LessonDto(
            $validated['topic_id'],
            $validated['level'],
            $user->native_lang,
            $user->learning_lang,
            $validated['themes'] ?? []
        );

        $data = $this->openAIService->generateLesson($lessonDto);

        /** @var Lesson $lesson */
        $lesson = Lesson::create([
            'user_id' => Auth::id(),
            'topic_id' => $validated['topic_id'],
            'lesson_name' => $data['lesson_name'] ?? '',
            'lesson_text' => $data['lesson_text'] ?? '',
            'grammar_data' => $data['grammar_highlights'] ?? [],
            'vocabulary' => $data['vocabulary'] ?? [],
            'quiz' => $data['quiz'] ?? [],
        ]);

        return redirect()->route('lessons.show', $lesson->id);
    }

    public function show(Lesson $lesson): Factory|View
    {
        abort_if($lesson->user_id !== Auth::id(), 403);

        return view('lessons.show', compact('lesson'));
    }
}
