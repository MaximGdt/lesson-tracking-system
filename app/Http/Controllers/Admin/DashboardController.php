<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        // Basic statistics
        $stats = [
            'users_count' => User::count(),
            'groups_count' => Group::active()->count(),
            'lessons_today' => Schedule::whereDate('date', today())
                ->notCancelled()
                ->count(),
            'lessons_month' => Lesson::whereHas('schedule', function ($q) {
                    $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                })
                ->where('is_conducted', true)
                ->count(),
        ];

        // Weekly data for chart
        $weeklyData = $this->getWeeklyData();

        // Lesson types data for chart
        $typesData = $this->getTypesData();

        // Recent marked lessons
        $recentLessons = Lesson::with(['schedule.teacher', 'schedule.group'])
            ->where('is_conducted', true)
            ->latest('marked_at')
            ->limit(10)
            ->get();

        // Upcoming schedules
        $upcomingSchedules = Schedule::with(['teacher', 'group'])
            ->upcoming()
            ->notCancelled()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'weeklyData',
            'typesData',
            'recentLessons',
            'upcomingSchedules'
        ));
    }

    /**
     * Get weekly lessons data for chart.
     */
    protected function getWeeklyData(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $data = Lesson::join('schedules', 'lessons.schedule_id', '=', 'schedules.id')
            ->whereBetween('schedules.date', [$startOfWeek, $endOfWeek])
            ->where('lessons.is_conducted', true)
            ->select(
                DB::raw('DATE(schedules.date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $values = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $labels[] = $date->translatedFormat('D');
            $values[] = $data->get($date->format('Y-m-d'))->count ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get lesson types data for chart.
     */
    protected function getTypesData(): array
    {
        $data = Schedule::join('lessons', 'schedules.id', '=', 'lessons.schedule_id')
            ->where('lessons.is_conducted', true)
            ->whereMonth('schedules.date', now()->month)
            ->select('schedules.type', DB::raw('COUNT(*) as count'))
            ->groupBy('schedules.type')
            ->get();

        $typeNames = [
            'lecture' => 'Лекции',
            'practice' => 'Практики',
            'lab' => 'Лабораторные',
            'exam' => 'Экзамены',
            'consultation' => 'Консультации',
        ];

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $typeNames[$item->type] ?? $item->type;
            $values[] = $item->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}