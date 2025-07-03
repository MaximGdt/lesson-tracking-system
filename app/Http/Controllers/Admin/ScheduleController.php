<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Group;
use App\Models\User;
use App\Models\Holiday;
use App\Events\ScheduleChanged;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $schedules = Schedule::with(['group', 'teacher', 'lesson'])
            ->when($request->date, function ($query, $date) {
                $query->whereDate('date', $date);
            })
            ->when($request->group_id, function ($query, $groupId) {
                $query->where('group_id', $groupId);
            })
            ->when($request->teacher_id, function ($query, $teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'cancelled') {
                    $query->where('is_cancelled', true);
                } elseif ($status === 'active') {
                    $query->where('is_cancelled', false);
                }
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $groups = Group::active()->get();
        $teachers = User::teachers()->active()->get();

        return view('admin.schedules.index', compact('schedules', 'groups', 'teachers'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $groups = Group::active()->with('teachers')->get();
        $teachers = User::teachers()->active()->get();
        $holidays = Holiday::where('date', '>=', today())->get();
        
        return view('admin.schedules.create', compact('groups', 'teachers', 'holidays'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:lecture,practice,lab,exam,consultation'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if date is holiday
        if (Holiday::isDayOff($validated['date'])) {
            return back()->withInput()->with('warning', 'Выбранная дата является выходным днем.');
        }

        // Check for conflicts
        $conflict = Schedule::where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('teacher_id', $validated['teacher_id'])
                    ->orWhere('group_id', $validated['group_id']);
            })
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'На это время уже есть занятие для выбранного преподавателя или группы.');
        }

        $schedule = Schedule::create($validated);

        // Create empty lesson record
        $schedule->lesson()->create(['is_conducted' => false]);

        // Fire event
        event(new ScheduleChanged($schedule, 'created'));

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно создано.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['group.students', 'teacher', 'lesson.logs.user']);
        
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the schedule.
     */
    public function edit(Schedule $schedule)
    {
        $groups = Group::active()->get();
        $teachers = User::teachers()->active()->get();
        
        return view('admin.schedules.edit', compact('schedule', 'groups', 'teachers'));
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:lecture,practice,lab,exam,consultation'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldData = $schedule->toArray();
        $schedule->update($validated);

        // Fire event if significant changes
        if ($oldData['date'] != $schedule->date || 
            $oldData['start_time'] != $schedule->start_time ||
            $oldData['teacher_id'] != $schedule->teacher_id) {
            event(new ScheduleChanged($schedule, 'updated'));
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно обновлено.');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule)
    {
        // Check if lesson was conducted
        if ($schedule->lesson && $schedule->lesson->is_conducted) {
            return back()->with('error', 'Нельзя удалить проведенное занятие.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно удалено.');
    }

    /**
     * Cancel schedule.
     */
    public function cancel(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ]);

        $schedule->update([
            'is_cancelled' => true,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        event(new ScheduleChanged($schedule, 'cancelled'));

        return back()->with('success', 'Занятие отменено.');
    }

    /**
     * Bulk create schedules.
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:lecture,practice,lab,exam,consultation'],
            'room' => ['nullable', 'string', 'max:50'],
            'day_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'skip_holidays' => ['boolean'],
        ]);

        $created = 0;
        $currentDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek == $validated['day_of_week']) {
                // Skip holidays if requested
                if ($validated['skip_holidays'] ?? true) {
                    if (Holiday::isDayOff($currentDate)) {
                        $currentDate->addWeek();
                        continue;
                    }
                }

                // Check for conflicts
                $conflict = Schedule::where('date', $currentDate)
                    ->where(function ($query) use ($validated) {
                        $query->where('teacher_id', $validated['teacher_id'])
                            ->orWhere('group_id', $validated['group_id']);
                    })
                    ->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->exists();

                if (!$conflict) {
                    $schedule = Schedule::create([
                        'group_id' => $validated['group_id'],
                        'teacher_id' => $validated['teacher_id'],
                        'subject' => $validated['subject'],
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'],
                        'room' => $validated['room'],
                        'type' => $validated['type'],
                    ]);

                    $schedule->lesson()->create(['is_conducted' => false]);
                    $created++;
                }

                $currentDate->addWeek();
            } else {
                $currentDate->addDay();
            }
        }

        return back()->with('success', "Создано занятий: {$created}");
    }
}