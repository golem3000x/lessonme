<?php

use App\Enums\System\SystemLanguageEnum;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ZaloController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Lesson\LessonController;
use App\Http\Middleware\EnsureTempGuestUser;
use App\Livewire\Auth\LanguageSelection;
use App\Livewire\Auth\RoleSelection;
use App\Livewire\Course\CourseIndex;
use App\Livewire\Lesson\LessonIndex;
use App\Livewire\Lesson\ManualLessonCreator;
use App\Livewire\Messages\DirectMessages;
use App\Livewire\Messages\MessagesIndex;
use App\Livewire\Onboarding\ProfileSetup;
use App\Livewire\Settings\UserSettings;
use App\Livewire\Topic\TopicIndex;
use App\Livewire\User\UserList;
use App\Livewire\User\UserProfile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
})->middleware(['guest', EnsureTempGuestUser::class]);

Route::get('/support', function () {
    return view('support');
})->name('support');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, SystemLanguageEnum::toArray(), true)) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/language', LanguageSelection::class)->name('auth.language');
    Route::get('/auth/role', RoleSelection::class)->name('auth.role');

    // Password reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

    //    Route::get('/auth/zalo', [ZaloController::class, 'redirect'])->name('zalo.login');
    //    Route::get('/auth/zalo/callback', [ZaloController::class, 'callback']);

    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('facebook.login');
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
});

// Lesson show route - accessible without auth for demo lessons
// Use UUID constraint to avoid catching /lessons/create
Route::get('/lessons/{lesson}', [LessonController::class, 'show'])
    ->name('lessons.show')
    ->whereUuid('lesson');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', ProfileSetup::class)->name('profile.edit');

    // Users / Community
    Route::get('/users', UserList::class)->name('users.index');
    Route::get('/profile/{user}', UserProfile::class)->name('profile.show');

    // Settings
    Route::get('/settings', UserSettings::class)->name('settings.index');

    // Direct Messages
    Route::get('/messages', MessagesIndex::class)->name('messages.index');
    Route::get('/messages/{user}', DirectMessages::class)->name('messages.chat');

    // Topics management
    Route::get('/manage-topics', TopicIndex::class)->name('topics.index');

    // Lessons management
    Route::get('/lessons', LessonIndex::class)->name('lessons.index');
    Route::get('/lessons/create', ManualLessonCreator::class)->name('lessons.create');

    // Courses management
    Route::get('/courses', CourseIndex::class)->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create')->middleware('teacher');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit')->middleware('teacher');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Protected lesson routes - require ownership
    Route::middleware('lesson.owner')->group(function () {
        Route::get('/lessons/{lesson}/history', [LessonController::class, 'history'])->name('lessons.history');
        Route::post('/lessons/{lesson}/history/{history}/restore', [LessonController::class, 'restore'])->name('lessons.history.restore');
        Route::get('/lessons/{lesson}/scores', [LessonController::class, 'scores'])->name('lessons.scores');
    });

    // Help Center routes
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::get('/getting-started', [HelpController::class, 'gettingStarted'])->name('getting-started');
        Route::get('/for-students', [HelpController::class, 'forStudents'])->name('for-students');
        Route::get('/for-teachers', [HelpController::class, 'forTeachers'])->name('for-teachers');
        Route::get('/exercise-types', [HelpController::class, 'exerciseTypes'])->name('exercise-types');
        Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
        Route::get('/courses', [HelpController::class, 'courses'])->name('courses');
        Route::get('/find-users', [HelpController::class, 'findUsers'])->name('find-users');
        Route::get('/ai-generation', [HelpController::class, 'aiGeneration'])->name('ai-generation');
    });
});
