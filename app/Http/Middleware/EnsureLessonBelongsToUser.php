<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Lesson\Lesson;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnsureLessonBelongsToUser
{
    /**
     * Handle an incoming request.
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

        // Allow access if user is authenticated and owns the lesson
        if ($request->user() && $request->user()->id === $lesson->user_id) {
            return $next($request);
        }

        // Allow access to temp guest users' lessons (for demo purposes)
        $lesson->loadMissing('user');
        // @phpstan-ignore-next-line - user can be null even after loadMissing
        if ($lesson->user && $lesson->user->is_temp_guest) {
            return $next($request);
        }

        // Redirect to lessons index with error message
        abort(Response::HTTP_FORBIDDEN, __('lessons.access_denied'));
    }
}
