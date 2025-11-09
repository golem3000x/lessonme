<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Factory;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(Lesson $lesson): Factory|View
    {
        abort_if($lesson->user_id !== Auth::id(), 403);

        return view('lessons.show', compact('lesson'));
    }
}
