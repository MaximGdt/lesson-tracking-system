<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } elseif (auth()->check() && auth()->user()->locale) {
            App::setLocale(auth()->user()->locale);
        }

        return $next($request);
    }
}
