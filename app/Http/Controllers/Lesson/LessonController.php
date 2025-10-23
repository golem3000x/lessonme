<?php
declare(strict_types=1);

namespace App\Http\Controllers\Lesson;

use App\Dto\LessonDto;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Topic;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Illuminate\View\Factory;

class LessonController extends Controller
{
    public function __construct(
        private readonly OpenAIService $openAIService,
    )
    {
    }

    public function index(): Factory|View
    {
        $lessons = Lesson::where('user_id', Auth::id())->latest()->get();
        return view('lessons.index', compact('lessons'));
    }

    public function create(): Factory|View
    {
        return view('lessons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'topic' => 'nullable|string|max:255', //проверка что имя topic в enum
            'level' => 'required|string',
            'themes' => 'nullable|array',
        ]);

        $user = Auth::user();

        $topic_id = //

        $lessonDto = new LessonDto(
            $request->topic,
            $request->level,
            $user->native_lang,
            $user->learning_lang,
            $request->themes ?? []
        );

        $data = $this->openAIService->generateLesson($lessonDto);

        /** @var Lesson $lesson */
        $lesson = Lesson::create([
            'user_id' => Auth::id(),
            'topic_id' => $topic_id,
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
