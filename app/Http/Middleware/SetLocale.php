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
        // Check if locale is set in session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } 
        // Check if user has preferred locale
        elseif ($request->user() && $request->user()->locale) {
            App::setLocale($request->user()->locale);
        }
        // Default to Ukrainian
        else {
            App::setLocale('uk');
        }
        
        return $next($request);
    }
}