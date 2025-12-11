<?php

use App\Http\Middleware\EnsureLessonAccess;
use App\Http\Middleware\EnsureLessonBelongsToUser;
use App\Http\Middleware\EnsureUserIsTeacher;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Livewire\LivewireServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', SetLocale::class);
        $middleware->alias([
            'lesson.owner' => EnsureLessonBelongsToUser::class,
            'lesson.access' => EnsureLessonAccess::class,
            'teacher' => EnsureUserIsTeacher::class,
        ]);
    })
    ->withProviders([
        LivewireServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
