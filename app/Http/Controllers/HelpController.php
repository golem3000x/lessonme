<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the Help Center index page
     */
    public function index(): View
    {
        return view('help.index');
    }

    /**
     * Display Getting Started section
     */
    public function gettingStarted(): View
    {
        return view('help.getting-started');
    }

    /**
     * Display For Students section
     */
    public function forStudents(): View
    {
        return view('help.for-students');
    }

    /**
     * Display For Teachers section
     */
    public function forTeachers(): View
    {
        return view('help.for-teachers');
    }

    /**
     * Display Exercise Types section
     */
    public function exerciseTypes(): View
    {
        return view('help.exercise-types');
    }

    /**
     * Display FAQ section
     */
    public function faq(): View
    {
        return view('help.faq');
    }
}
