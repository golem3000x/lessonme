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
        // Проверяем локаль в сессии
        $locale = session('locale');
        
        // Если локаль не в сессии, пытаемся взять из native_lang пользователя
        if (! $locale) {
            $user = auth()->user();
            if ($user && property_exists($user, 'native_lang') && $user->native_lang) {
                $locale = $user->native_lang;
                // Сохраняем в сессию для последующих запросов
                session(['locale' => $locale]);
            }
        }
        
        // Устанавливаем локаль, если она валидна
        if (is_string($locale) && in_array($locale, SystemLanguageEnum::toArray(), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
