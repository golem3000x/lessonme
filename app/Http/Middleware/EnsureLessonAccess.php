<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Lesson\Lesson;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnsureLessonAccess
{
    /**
     * Handle an incoming request.
     * Allows access to:
     * - Lesson owner (creator)
     * - Assigned students
     * - Temp guest users' lessons (for demo purposes)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $lesson = $request->route('lesson');

        // If lesson is not found or not a Lesson instance, let controller handle 404
        if (! $lesson instanceof Lesson) {
            return $next($request);
        }

        $user = $request->user();

        // Guest users cannot access lessons
        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, __('lessons.login_required'));
        }

        // 1. Allow access if user is the lesson owner
        if ($user->id === $lesson->user_id) {
            return $next($request);
        }

        // 2. Allow access if user is assigned to this lesson as a student
        $is_assigned = $lesson->assignments()
            ->where('student_id', '=', $user->id)
            ->exists();

        if ($is_assigned) {
            return $next($request);
        }

        // 3. Allow access to temp guest users' lessons (for demo purposes)
        $lesson->loadMissing('user');
        // @phpstan-ignore-next-line - user can be null even after loadMissing
        if ($lesson->user && $lesson->user->is_temp_guest) {
            return $next($request);
        }

        // Deny access
        abort(Response::HTTP_FORBIDDEN, __('lessons.access_denied'));
    }
}
