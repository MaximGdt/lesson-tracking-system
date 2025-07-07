<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{

    /**
     * Установить язык интерфейса
     */
    public function setLocale(Request $request, $locale)
    {
        Log::info('Locale change attempt', [
            'requested_locale' => $locale,
            'current_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
        ]);

        $supportedLocales = ['uk', 'en', 'ru'];

        if (!in_array($locale, $supportedLocales)) {
            Log::warning('Invalid locale requested', ['locale' => $locale]);
            return redirect()->back();
        }

        // Устанавливаем локаль в сессию
        Session::put('locale', $locale);
        Session::save(); // Принудительно сохраняем сессию

        // Устанавливаем локаль для текущего запроса
        App::setLocale($locale);

        // Если пользователь авторизован, сохраняем в БД
        if ($request->user()) {
           $request->user()->update(['locale' => $locale]);
            Log::info('Locale saved to user', [
                'user_id' => $request->user()->id,
                'locale' => $locale,
                'Auth_user' => $request->user(),
                'Auth_user_2' => auth()->user(),
            ]);
        }

        Log::info('Locale changed successfully', [
            'new_locale' => $locale,
            'session_locale_after' => Session::get('locale'),
        ]);

        return redirect()->back()->with('success', 'Language changed successfully');
    }

}
