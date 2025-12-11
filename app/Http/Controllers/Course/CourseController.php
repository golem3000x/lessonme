<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use Illuminate\View\Factory;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function show(Course $course): Factory|View
    {
        return view('courses.show', compact('course'));
    }

    public function create(): Factory|View
    {
        return view('courses.create');
    }

    public function edit(Course $course): Factory|View
    {
        return view('courses.edit', compact('course'));
    }
}
