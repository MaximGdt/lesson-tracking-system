<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Отладочный маршрут
Route::get('/debug-locale', function () {
    return [
        'app_locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'config_locale' => config('app.locale'),
        'user_locale' => auth()->user()->locale ?? 'not logged in',
        'session_all' => session()->all(),
    ];
});

Route::get('/test-locale', function () {
    return view('test-locale');
})->middleware(['web']);


// В самом начале файла, перед всеми группами
Route::get('/test-locale-simple', function () {
    return response()->json([
        'locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'session_all' => session()->all(),
        'translations' => [
            'welcome' => __('app.welcome'),
            'dashboard' => __('app.dashboard'),
        ]
    ]);
})->middleware('web');

Route::get('/test-session', function () {
    session(['test' => 'value']);
    return [
        'session_id' => session()->getId(),
        'test' => session('test'),
        'locale' => session('locale'),
        'all' => session()->all(),
    ];
});

// Маршрут переключения языка (доступен всегда)
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale.set');

// Гостевые маршруты
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Авторизованные маршруты
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Админские маршруты
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [LoginController::class, 'home'])->name('home');

        // Управление пользователями
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Управление группами
        Route::resource('groups', GroupController::class);
        Route::post('groups/{group}/sync-students', [GroupController::class, 'syncStudents'])->name('groups.sync-students');

        // Управление расписанием
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/{schedule}/cancel', [ScheduleController::class, 'cancel'])->name('schedules.cancel');
        Route::post('schedules/bulk-create', [ScheduleController::class, 'bulkCreate'])->name('schedules.bulk-create');

        // Отчеты
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });

    // Маршруты преподавателя
    Route::middleware(['role:teacher,admin,super_admin'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

        // Занятия
        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/today', [LessonController::class, 'today'])->name('lessons.today');
        Route::get('/lessons/{schedule}', [LessonController::class, 'show'])->name('lessons.show');
        Route::post('/lessons/{schedule}/mark-conducted', [LessonController::class, 'markConducted'])->name('lessons.mark-conducted');
        Route::post('/lessons/{schedule}/mark-not-conducted', [LessonController::class, 'markNotConducted'])->name('lessons.mark-not-conducted');

        // Расписание
        Route::get('/schedule', [TeacherScheduleController::class, 'index'])->name('schedule');
        Route::get('/schedule/calendar', [TeacherScheduleController::class, 'calendar'])->name('schedule.calendar');
    });

    // Маршруты профиля (для всех авторизованных пользователей)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
});

// API маршруты для календаря
Route::middleware(['auth', 'throttle:api'])->prefix('api')->group(function () {
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('api.calendar.events');
    Route::get('/holidays/{year}', [HolidayController::class, 'index'])->name('api.holidays');
});
