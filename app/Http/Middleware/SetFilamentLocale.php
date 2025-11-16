<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Приоритет 1: Язык из сессии (переключатель)
        if (session()->has('locale')) {
            $locale = session('locale');
            if (in_array($locale, ['en', 'vi'])) {
                App::setLocale($locale);
                return $next($request);
            }
        }
        
        // Приоритет 2: Язык пользователя из БД
        $user = auth()->user();
        if ($user && property_exists($user, 'native_lang')) {
            $locale = in_array($user->native_lang, ['en', 'vi']) 
                ? $user->native_lang 
                : 'en';
            
            App::setLocale($locale);
        }
        
        return $next($request);
    }
}
