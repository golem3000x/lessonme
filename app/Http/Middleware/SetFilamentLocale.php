<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\System\SystemLanguageEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            $locale = session('locale');
            if (in_array($locale, SystemLanguageEnum::toArray(), true)) {
                App::setLocale($locale);

                return $next($request);
            }
        }

        $user = auth()->user();
        if ($user && property_exists($user, 'native_lang')) {
            $locale = in_array($user->native_lang, SystemLanguageEnum::toArray(), true)
                ? $user->native_lang
                : SystemLanguageEnum::ENGLISH->value;

            App::setLocale($locale);
        }

        return $next($request);
    }
}
