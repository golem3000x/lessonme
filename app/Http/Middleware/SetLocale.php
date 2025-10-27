<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\SystemLanguageEnum;
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
        $locale = session('locale');
        if (is_string($locale) && in_array($locale, SystemLanguageEnum::toArray(), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
