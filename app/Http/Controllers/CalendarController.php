<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get calendar events for API.
     */
    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        
        $query = Schedule::with(['group', 'teacher', 'lesson']);
        
        // Filter by date range if provided
        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }
        
        // Filter by teacher if not admin
        if (!auth()->user()->isAdmin()) {
            $query->where('teacher_id', auth()->id());
        }
        
        $schedules = $query->get();
        
        $events = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->group->code . ' - ' . $schedule->subject,
                'start' => $schedule->date->format('Y-m-d') . 'T' . $schedule->start_time,
                'end' => $schedule->date->format('Y-m-d') . 'T' . $schedule->end_time,
                'url' => auth()->user()->isAdmin() 
                    ? route('admin.schedules.show', $schedule)
                    : route('teacher.lessons.show', $schedule),
                'color' => $this->getEventColor($schedule),
                'extendedProps' => [
                    'group' => $schedule->group->name,
                    'teacher' => $schedule->teacher->full_name,
                    'room' => $schedule->room,
                    'type' => $schedule->type_display,
                    'is_conducted' => $schedule->lesson && $schedule->lesson->is_conducted,
                    'is_cancelled' => $schedule->is_cancelled,
                ]
            ];
        });
        
        return response()->json($events);
    }
    
    /**
     * Get event color based on schedule status.
     */
    protected function getEventColor(Schedule $schedule): string
    {
        if ($schedule->is_cancelled) {
            return '#6c757d'; // gray
        }
        
        if ($schedule->lesson && $schedule->lesson->is_conducted) {
            return '#28a745'; // green
        }
        
        if ($schedule->isPast()) {
            return '#ffc107'; // yellow (warning)
        }
        
        return '#007bff'; // blue (default)
    }
}