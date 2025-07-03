<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display teacher's schedule.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();
        
        // Get date range
        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfWeek();
            
        $endDate = $request->has('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfWeek();
        
        // Get schedules
        $schedules = Schedule::with(['group', 'lesson'])
            ->forTeacher($teacher->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($schedule) {
                return $schedule->date->format('Y-m-d');
            });
        
        // Get groups for filter
        $groups = $teacher->groups;
        
        return view('teacher.schedule', compact('schedules', 'startDate', 'endDate', 'groups'));
    }
    
    /**
     * Get calendar view of schedule.
     */
    public function calendar(Request $request)
    {
        $teacher = auth()->user();
        
        // Get month
        $month = $request->has('month') 
            ? Carbon::parse($request->month) 
            : now();
            
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        // Get schedules for the month
        $schedules = Schedule::with(['group', 'lesson'])
            ->forTeacher($teacher->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->group->code . ' - ' . $schedule->subject,
                    'start' => $schedule->date->format('Y-m-d') . 'T' . $schedule->start_time,
                    'end' => $schedule->date->format('Y-m-d') . 'T' . $schedule->end_time,
                    'url' => route('teacher.lessons.show', $schedule),
                    'color' => $schedule->is_cancelled ? '#6c757d' : 
                              ($schedule->lesson && $schedule->lesson->is_conducted ? '#28a745' : '#007bff'),
                    'extendedProps' => [
                        'group' => $schedule->group->name,
                        'room' => $schedule->room,
                        'type' => $schedule->type_display,
                        'is_conducted' => $schedule->lesson && $schedule->lesson->is_conducted,
                        'is_cancelled' => $schedule->is_cancelled,
                    ]
                ];
            });
        
        if ($request->wantsJson()) {
            return response()->json($schedules);
        }
        
        return view('teacher.schedule-calendar', compact('month', 'schedules'));
    }
}