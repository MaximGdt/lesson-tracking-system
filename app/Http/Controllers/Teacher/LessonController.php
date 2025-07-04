<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Lesson;
use App\Events\LessonMarked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    /**
     * Display teacher's schedule with lesson marks.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();
        
        $schedules = Schedule::with(['group', 'lesson'])
            ->forTeacher($teacher->id)
            ->when($request->date_from, function ($query, $date) {
                $query->where('date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->where('date', '<=', $date);
            })
            ->when($request->group_id, function ($query, $groupId) {
                $query->where('group_id', $groupId);
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'conducted') {
                    $query->whereHas('lesson', function ($q) {
                        $q->where('is_conducted', true);
                    });
                } elseif ($status === 'not_conducted') {
                    $query->where(function ($q) {
                        $q->whereDoesntHave('lesson')
                            ->orWhereHas('lesson', function ($subQ) {
                                $subQ->where('is_conducted', false);
                            });
                    });
                }
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $groups = $teacher->groups;

        return view('teacher.lesson.index', compact('schedules', 'groups'));
    }

    /**
     * Mark lesson as conducted.
     */
    public function markConducted(Request $request, Schedule $schedule)
    {
        // Check if teacher has access to this schedule
        if ($schedule->teacher_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет доступа к этому занятию.');
        }

        // Check if schedule is not in the future
        if ($schedule->isFuture() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Нельзя отметить будущие занятия.');
        }

        // Check if schedule is cancelled
        if ($schedule->is_cancelled) {
            return back()->with('error', 'Нельзя отметить отмененное занятие.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'students_present' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($schedule, $validated) {
            $lesson = $schedule->lesson ?? new Lesson(['schedule_id' => $schedule->id]);
            
            if (!$lesson->exists) {
                $lesson->save();
            }

            $lesson->markAsConducted(
                auth()->user(),
                $validated['notes'] ?? null,
                $validated['students_present'] ?? null
            );

            // Fire event for notifications
            event(new LessonMarked($lesson));
        });

        return back()->with('success', 'Занятие успешно отмечено как проведенное.');
    }

    /**
     * Mark lesson as not conducted.
     */
    public function markNotConducted(Schedule $schedule)
    {
        // Check if teacher has access to this schedule
        if ($schedule->teacher_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет доступа к этому занятию.');
        }

        // Check if lesson exists and is conducted
        if (!$schedule->lesson || !$schedule->lesson->is_conducted) {
            return back()->with('error', 'Занятие не отмечено как проведенное.');
        }

        DB::transaction(function () use ($schedule) {
            $schedule->lesson->markAsNotConducted(auth()->user());
        });

        return back()->with('success', 'Отметка о проведении занятия снята.');
    }

    /**
     * Show lesson details.
     */
    public function show(Schedule $schedule)
    {
        // Check if teacher has access to this schedule
        if ($schedule->teacher_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'У вас нет доступа к этому занятию.');
        }

        $schedule->load(['group.students' => function ($query) {
            $query->active()->orderBy('last_name');
        }, 'lesson.logs.user']);

        return view('teacher.lessons.show', compact('schedule'));
    }

    /**
     * Get today's lessons for quick marking.
     */
    public function today()
    {
        $teacher = auth()->user();
        
        $schedules = Schedule::with(['group', 'lesson'])
            ->forTeacher($teacher->id)
            ->whereDate('date', today())
            ->notCancelled()
            ->orderBy('start_time')
            ->get();

        return view('teacher.lesson.today', compact('schedules'));
    }
}