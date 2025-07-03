<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports page.
     */
    public function index()
    {
        $teachers = User::teachers()->active()->get();
        $groups = Group::active()->get();
        
        return view('admin.reports.index', compact('teachers', 'groups'));
    }

    /**
     * Generate report based on type.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => ['required', 'in:lessons,teacher_workload,group_attendance'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'format' => ['required', 'in:view,excel,pdf'],
        ]);

        $filters = [
            'date_from' => $validated['date_from'],
            'date_to' => $validated['date_to'],
            'teacher_id' => $validated['teacher_id'],
            'group_id' => $validated['group_id'],
        ];

        switch ($validated['report_type']) {
            case 'lessons':
                $data = $this->reportService->generateLessonsReport($filters);
                break;
            case 'teacher_workload':
                $data = $this->reportService->getTeacherWorkload($filters);
                break;
            case 'group_attendance':
                $data = $this->reportService->getGroupAttendance($filters);
                break;
        }

        // Store data in session for export
        session(['report_data' => $data, 'report_type' => $validated['report_type']]);

        if ($validated['format'] === 'excel') {
            return $this->exportExcel($validated['report_type'], $data);
        } elseif ($validated['format'] === 'pdf') {
            return $this->exportPdf($validated['report_type'], $data);
        }

        return view('admin.reports.' . $validated['report_type'], $data);
    }

    /**
     * Export report to Excel.
     */
    protected function exportExcel(string $type, array $data)
    {
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.xlsx';
        return $this->reportService->exportToExcel($data, $filename);
    }

    /**
     * Export report to PDF.
     */
    protected function exportPdf(string $type, array $data)
    {
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.pdf';
        return $this->reportService->exportToPdf($data, $filename);
    }

    /**
     * Export stored report.
     */
    public function export(string $type)
    {
        $data = session('report_data');
        $reportType = session('report_type');
        
        if (!$data || !$reportType) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Нет данных для экспорта. Сначала сгенерируйте отчет.');
        }

        if ($type === 'excel') {
            return $this->exportExcel($reportType, $data);
        } elseif ($type === 'pdf') {
            return $this->exportPdf($reportType, $data);
        }

        return back()->with('error', 'Неверный формат экспорта.');
    }
}