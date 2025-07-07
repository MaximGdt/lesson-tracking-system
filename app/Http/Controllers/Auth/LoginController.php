<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
        return redirect()->route('admin.dashboard'); // или на любой дашборд по ролям
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Update last login time
            $user->update(['last_login_at' => now()]);

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account is deactivated.'],
                ]);
            }

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isTeacher()) {
                return redirect()->intended(route('teacher.dashboard'));
            }

            return redirect()->intended('/');
        }
            $locale = config('app.locale', 'uk');
            Session::put('locale', $locale);
           if ($locale == 'en') {
             throw ValidationException::withMessages([
                'email' => ['Wrong email or password.'],

                ]);
           } elseif ($locale == 'uk') {
            throw ValidationException::withMessages([
                'email' => ['Не вірний email або пароль'],
            ]);
           } elseif ($locale == 'ru') {
            throw ValidationException::withMessages([
                'email' => ['Не правельный логин или пароль'],
            ]);
           }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function home(Request $request){

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->intended('/index.php');
        } elseif ($user->isTeacher()) {
            return redirect()->intended('/index.php');
        }

    }
}
