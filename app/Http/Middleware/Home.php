<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Home
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('home');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'У вас нет доступа к этому разделу.');
        }

        return $next($request);
    }
}