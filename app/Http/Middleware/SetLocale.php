<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\System\SystemLanguageEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check locale in session
        $locale = session('locale');

        // If locale not in session, try to get it from user's native_lang
        if (! $locale) {
            $user = auth()->user();
            if ($user && property_exists($user, 'native_lang') && $user->native_lang) {
                $locale = $user->native_lang;
                // Save to session for subsequent requests
                session(['locale' => $locale]);
            }
        }

        // Set locale if it's valid
        if (is_string($locale) && in_array($locale, SystemLanguageEnum::toArray(), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
