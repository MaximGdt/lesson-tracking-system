<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Lesson;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display teacher dashboard.
     */
    public function index()
    {
        $teacher = auth()->user();
        
        // Today's schedules
        $todaySchedules = Schedule::with(['group', 'lesson'])
            ->forTeacher($teacher->id)
            ->whereDate('date', today())
            ->notCancelled()
            ->orderBy('start_time')
            ->get();

        // This week statistics
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $weekSchedules = Schedule::forTeacher($teacher->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->notCancelled()
            ->get();

        $weekStats = [
            'total' => $weekSchedules->count(),
            'conducted' => $weekSchedules->filter(function ($schedule) {
                return $schedule->lesson && $schedule->lesson->is_conducted;
            })->count(),
            'pending' => $weekSchedules->filter(function ($schedule) {
                return !$schedule->lesson || !$schedule->lesson->is_conducted;
            })->count(),
        ];

        // Month statistics
        $monthSchedules = Schedule::forTeacher($teacher->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->notCancelled()
            ->get();

        $monthStats = [
            'total' => $monthSchedules->count(),
            'conducted' => $monthSchedules->filter(function ($schedule) {
                return $schedule->lesson && $schedule->lesson->is_conducted;
            })->count(),
        ];

        // Recent marked lessons
        $recentLessons = Lesson::with(['schedule.group'])
            ->whereHas('schedule', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('is_conducted', true)
            ->latest('marked_at')
            ->limit(5)
            ->get();

        // Upcoming schedules
        $upcomingSchedules = Schedule::with(['group'])
            ->forTeacher($teacher->id)
            ->where('date', '>', today())
            ->notCancelled()
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Groups
        $groups = $teacher->groups()->withCount('students')->get();

        return view('teacher.dashboard', compact(
            'todaySchedules',
            'weekStats',
            'monthStats',
            'recentLessons',
            'upcomingSchedules',
            'groups'
        ));
    }
}