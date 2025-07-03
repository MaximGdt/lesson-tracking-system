<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LessonsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Generate lessons report by period.
     */
    public function generateLessonsReport(array $filters): array
    {
        $query = Lesson::query()
            ->join('schedules', 'lessons.schedule_id', '=', 'schedules.id')
            ->where('lessons.is_conducted', true);

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->where('schedules.date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('schedules.date', '<=', $filters['date_to']);
        }

        // Apply teacher filter
        if (!empty($filters['teacher_id'])) {
            $query->where('schedules.teacher_id', $filters['teacher_id']);
        }

        // Apply group filter
        if (!empty($filters['group_id'])) {
            $query->where('schedules.group_id', $filters['group_id']);
        }

        // Get summary statistics
        $summary = [
            'total_lessons' => $query->count(),
            'total_hours' => $this->calculateTotalHours($query->get()),
            'by_type' => $this->getLessonsByType($query),
            'by_teacher' => $this->getLessonsByTeacher($query),
            'by_group' => $this->getLessonsByGroup($query),
            'by_month' => $this->getLessonsByMonth($query),
        ];

        // Get detailed data
        $lessons = $query->with(['schedule.teacher', 'schedule.group', 'markedBy'])
            ->orderBy('schedules.date', 'desc')
            ->get();

        return [
            'summary' => $summary,
            'lessons' => $lessons,
            'filters' => $filters,
        ];
    }

    /**
     * Calculate total hours from lessons.
     */
    protected function calculateTotalHours(Collection $lessons): float
    {
        $totalMinutes = 0;
        
        foreach ($lessons as $lesson) {
            $start = Carbon::parse($lesson->schedule->start_time);
            $end = Carbon::parse($lesson->schedule->end_time);
            $totalMinutes += $end->diffInMinutes($start);
        }
        
        return round($totalMinutes / 60, 2);
    }

    /**
     * Get lessons count by type.
     */
    protected function getLessonsByType($query): array
    {
        return DB::table('schedules')
            ->select('type', DB::raw('COUNT(*) as count'))
            ->whereIn('id', $query->pluck('schedule_id'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                $types = [
                    'lecture' => 'Лекции',
                    'practice' => 'Практики',
                    'lab' => 'Лабораторные',
                    'exam' => 'Экзамены',
                    'consultation' => 'Консультации',
                ];
                return [$types[$item->type] ?? $item->type => $item->count];
            })
            ->toArray();
    }

    /**
     * Get lessons count by teacher.
     */
    protected function getLessonsByTeacher($query): array
    {
        return DB::table('schedules')
            ->join('users', 'schedules.teacher_id', '=', 'users.id')
            ->select('users.id', 'users.last_name', 'users.first_name', DB::raw('COUNT(*) as count'))
            ->whereIn('schedules.id', $query->pluck('schedule_id'))
            ->groupBy('users.id', 'users.last_name', 'users.first_name')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                $name = $item->last_name . ' ' . mb_substr($item->first_name, 0, 1) . '.';
                return [$name => $item->count];
            })
            ->toArray();
    }

    /**
     * Get lessons count by group.
     */
    protected function getLessonsByGroup($query): array
    {
        return DB::table('schedules')
            ->join('groups', 'schedules.group_id', '=', 'groups.id')
            ->select('groups.code', 'groups.name', DB::raw('COUNT(*) as count'))
            ->whereIn('schedules.id', $query->pluck('schedule_id'))
            ->groupBy('groups.code', 'groups.name')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->code => [
                    'name' => $item->name,
                    'count' => $item->count,
                ]];
            })
            ->toArray();
    }

    /**
     * Get lessons count by month.
     */
    protected function getLessonsByMonth($query): array
    {
        return DB::table('schedules')
            ->select(
                DB::raw('EXTRACT(YEAR FROM date) as year'),
                DB::raw('EXTRACT(MONTH FROM date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereIn('id', $query->pluck('schedule_id'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                $monthNames = [
                    1 => 'Январь', 2 => 'Февраль', 3 => 'Март',
                    4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
                    7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
                    10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
                ];
                $key = $monthNames[$item->month] . ' ' . $item->year;
                return [$key => $item->count];
            })
            ->toArray();
    }

    /**
     * Export report to Excel.
     */
    public function exportToExcel(array $data, string $filename = 'lessons_report.xlsx')
    {
        return Excel::download(new LessonsExport($data), $filename);
    }

    /**
     * Export report to PDF.
     */
    public function exportToPdf(array $data, string $filename = 'lessons_report.pdf')
    {
        $pdf = Pdf::loadView('reports.lessons-pdf', $data);
        return $pdf->download($filename);
    }

    /**
     * Get teacher workload report.
     */
    public function getTeacherWorkload(array $filters): array
    {
        $query = User::teachers()->with(['schedules' => function ($q) use ($filters) {
            if (!empty($filters['date_from'])) {
                $q->where('date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $q->where('date', '<=', $filters['date_to']);
            }
        }]);

        $teachers = $query->get()->map(function ($teacher) {
            $schedules = $teacher->schedules;
            $conducted = $schedules->filter(function ($schedule) {
                return $schedule->lesson && $schedule->lesson->is_conducted;
            });

            return [
                'teacher' => $teacher,
                'total_scheduled' => $schedules->count(),
                'total_conducted' => $conducted->count(),
                'completion_rate' => $schedules->count() > 0 
                    ? round(($conducted->count() / $schedules->count()) * 100, 2) 
                    : 0,
                'total_hours' => $this->calculateScheduleHours($schedules),
                'conducted_hours' => $this->calculateScheduleHours($conducted),
            ];
        });

        return [
            'teachers' => $teachers,
            'filters' => $filters,
        ];
    }

    /**
     * Calculate total hours from schedules.
     */
    protected function calculateScheduleHours(Collection $schedules): float
    {
        $totalMinutes = 0;
        
        foreach ($schedules as $schedule) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $totalMinutes += $end->diffInMinutes($start);
        }
        
        return round($totalMinutes / 60, 2);
    }

    /**
     * Get group attendance report.
     */
    public function getGroupAttendance(array $filters): array
    {
        $query = Group::active()->with(['schedules' => function ($q) use ($filters) {
            $q->with('lesson');
            if (!empty($filters['date_from'])) {
                $q->where('date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $q->where('date', '<=', $filters['date_to']);
            }
        }, 'students' => function ($q) {
            $q->active();
        }]);

        if (!empty($filters['group_id'])) {
            $query->where('id', $filters['group_id']);
        }

        $groups = $query->get()->map(function ($group) {
            $schedules = $group->schedules;
            $conducted = $schedules->filter(function ($schedule) {
                return $schedule->lesson && $schedule->lesson->is_conducted;
            });

            $totalStudents = $group->students->count();
            $totalAttendance = 0;
            $lessonsWithAttendance = 0;

            foreach ($conducted as $schedule) {
                if ($schedule->lesson->students_present !== null) {
                    $totalAttendance += $schedule->lesson->students_present;
                    $lessonsWithAttendance++;
                }
            }

            return [
                'group' => $group,
                'total_students' => $totalStudents,
                'total_scheduled' => $schedules->count(),
                'total_conducted' => $conducted->count(),
                'average_attendance' => $lessonsWithAttendance > 0 
                    ? round($totalAttendance / $lessonsWithAttendance, 2) 
                    : null,
                'attendance_rate' => $totalStudents > 0 && $lessonsWithAttendance > 0
                    ? round(($totalAttendance / ($lessonsWithAttendance * $totalStudents)) * 100, 2)
                    : null,
            ];
        });

        return [
            'groups' => $groups,
            'filters' => $filters,
        ];
    }
}