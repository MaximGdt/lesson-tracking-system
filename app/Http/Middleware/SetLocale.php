<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('SetLocale middleware executing', [
            'session_exists' => Session::isStarted(),
            'session_id' => Session::getId(),
            'session_locale' => Session::get('locale'),
            'request_url' => $request->fullUrl(),
        ]);

        $supportedLocales = ['uk', 'en', 'ru'];
        $locale = config('app.locale', 'uk'); // язык по умолчанию
        
        // Приоритет 1: Проверяем сессию
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $supportedLocales)) {
                $locale = $sessionLocale;
                Log::info('Locale from session', ['locale' => $locale]);
            }
        }
        // Приоритет 2: Проверяем настройки пользователя
        elseif ($request->user() && $request->user()->locale) {
            if (in_array($request->user()->locale, $supportedLocales)) {
                $locale = $request->user()->locale;
                Session::put('locale', $locale);
                Log::info('Locale from user', ['locale' => $locale]);
            }
        }
        // Приоритет 3: Проверяем язык браузера
        elseif ($preferredLocale = $request->getPreferredLanguage($supportedLocales)) {
            $locale = $preferredLocale;
            Session::put('locale', $locale);
            Log::info('Locale from browser', ['locale' => $locale]);
        }

        // Устанавливаем локаль
        App::setLocale($locale);
        
        Log::info('SetLocale middleware completed', [
            'final_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
        ]);

        return $next($request);
    }
}