<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
{
   
    
    $supportedLocales = ['uk', 'en'];
    
   
    if (Session::has('locale')) {
        $locale = Session::get('locale');
    } 
   
    elseif ($request->user() && $request->user()->locale) {
        $locale = $request->user()->locale;
    } 
    
    elseif ($request->getPreferredLanguage($supportedLocales)) {
        $locale = $request->getPreferredLanguage($supportedLocales);
    } 
    
    else {
        $locale = 'uk';
    }

    // Validate locale
    $locale = in_array($locale, $supportedLocales) ? $locale : 'uk';

    App::setLocale($locale);
    Session::put('locale', $locale);

    return $next($request);
}
}