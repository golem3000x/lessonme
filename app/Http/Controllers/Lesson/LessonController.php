<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonHistory;
use App\Services\Lesson\LessonHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(Lesson $lesson): Factory|View
    {
        $lesson->load('topic.translations');

        return view('lessons.show', compact('lesson'));
    }

    public function history(Lesson $lesson): Factory|View
    {
        // Authorization is handled by lesson.owner middleware
        $histories = $lesson->histories()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('lessons.history', compact('lesson', 'histories'));
    }

    public function restore(Lesson $lesson, LessonHistory $history, LessonHistoryService $history_service): RedirectResponse
    {
        // Authorization is handled by lesson.owner middleware
        abort_if($history->lesson_id !== $lesson->id, 404);

        // Restore selected version
        $history_service->restore($history);

        return redirect()
            ->route('lessons.index')
            ->with('success', __('lessons.version_restored'));
    }

    public function scores(Lesson $lesson): Factory|View
    {
        // Authorization is handled by lesson.owner middleware
        return view('lessons.scores', compact('lesson'));
    }
}
