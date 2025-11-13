<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ZaloController;
use App\Http\Controllers\Lesson\LessonController;
use App\Livewire\Lesson\LessonIndex;
use App\Livewire\Onboarding\ProfileSetup;
use App\Livewire\Onboarding\TopicSelector;
use App\Livewire\Topic\TopicIndex;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'vi'], true)) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/zalo', [ZaloController::class, 'redirect'])->name('zalo.login');
    Route::get('/auth/zalo/callback', [ZaloController::class, 'callback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/onboarding', ProfileSetup::class)->name('onboarding.profile');
    Route::get('/topics', TopicSelector::class)->name('onboarding.topics');

    // Topics management
    Route::get('/manage-topics', TopicIndex::class)->name('topics.index');

    // Lessons management
    Route::get('/lessons', LessonIndex::class)->name('lessons.index');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('/lessons/{lesson}/history', [LessonController::class, 'show'])->name('lessons.history');
});
