<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function setLocale(Request $request, $locale)
    {
        if (in_array($locale, ['uk', 'en'])) {
            Session::put('locale', $locale);
            
            if ($request->user()) {
                $request->user()->update(['locale' => $locale]);
            }
        }
        
        return redirect()->back();
    }
}