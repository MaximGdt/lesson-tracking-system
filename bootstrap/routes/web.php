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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('login');
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Users management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Groups management
        Route::resource('groups', GroupController::class);
        Route::post('groups/{group}/sync-students', [GroupController::class, 'syncStudents'])->name('groups.sync-students');
        
        // Schedule management
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/{schedule}/cancel', [ScheduleController::class, 'cancel'])->name('schedules.cancel');
        Route::post('schedules/bulk-create', [ScheduleController::class, 'bulkCreate'])->name('schedules.bulk-create');
        
        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });
    
    // Teacher routes
    Route::middleware(['role:teacher,admin,super_admin'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        
        // Lessons
        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/today', [LessonController::class, 'today'])->name('lessons.today');
        Route::get('/lessons/{schedule}', [LessonController::class, 'show'])->name('lessons.show');
        Route::post('/lessons/{schedule}/mark-conducted', [LessonController::class, 'markConducted'])->name('lessons.mark-conducted');
        Route::post('/lessons/{schedule}/mark-not-conducted', [LessonController::class, 'markNotConducted'])->name('lessons.mark-not-conducted');
        
        // Schedule
        Route::get('/schedule', [TeacherScheduleController::class, 'index'])->name('schedule');
        Route::get('/schedule/calendar', [TeacherScheduleController::class, 'calendar'])->name('schedule.calendar');
    });
    
    // Profile routes (for all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// API routes for calendar events
Route::middleware(['auth', 'throttle:api'])->prefix('api')->group(function () {
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('api.calendar.events');
    Route::get('/holidays/{year}', [HolidayController::class, 'index'])->name('api.holidays');
});