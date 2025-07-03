<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Services\ExternalApiService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of groups.
     */
    public function index(Request $request)
    {
        $groups = Group::withCount(['students', 'teachers'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->course, function ($query, $course) {
                $query->where('course', $course);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        $teachers = User::teachers()->active()->get();
        return view('admin.groups.create', compact('teachers'));
    }

    /**
     * Store a newly created group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:groups'],
            'description' => ['nullable', 'string'],
            'course' => ['required', 'integer', 'min:1', 'max:6'],
            'speciality' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'teachers' => ['array'],
            'teachers.*.id' => ['exists:users,id'],
            'teachers.*.subject' => ['required_with:teachers.*.id', 'string', 'max:255'],
        ]);

        $group = Group::create($validated);

        // Attach teachers with subjects
        if (!empty($validated['teachers'])) {
            foreach ($validated['teachers'] as $teacher) {
                if (!empty($teacher['id']) && !empty($teacher['subject'])) {
                    $group->teachers()->attach($teacher['id'], ['subject' => $teacher['subject']]);
                }
            }
        }

        // Sync students from external API
        $this->syncStudents($group);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Группа успешно создана.');
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group)
    {
        $group->load(['teachers', 'students' => function ($query) {
            $query->active()->orderBy('last_name');
        }]);

        $upcomingSchedules = $group->schedules()
            ->with('teacher')
            ->upcoming()
            ->limit(10)
            ->get();

        return view('admin.groups.show', compact('group', 'upcomingSchedules'));
    }

    /**
     * Show the form for editing the group.
     */
    public function edit(Group $group)
    {
        $teachers = User::teachers()->active()->get();
        $group->load('teachers');
        
        return view('admin.groups.edit', compact('group', 'teachers'));
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:groups,code,' . $group->id],
            'description' => ['nullable', 'string'],
            'course' => ['required', 'integer', 'min:1', 'max:6'],
            'speciality' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
            'teachers' => ['array'],
            'teachers.*.id' => ['exists:users,id'],
            'teachers.*.subject' => ['required_with:teachers.*.id', 'string', 'max:255'],
        ]);

        $group->update($validated);

        // Sync teachers with subjects
        $teachersData = [];
        if (!empty($validated['teachers'])) {
            foreach ($validated['teachers'] as $teacher) {
                if (!empty($teacher['id']) && !empty($teacher['subject'])) {
                    $teachersData[$teacher['id']] = ['subject' => $teacher['subject']];
                }
            }
        }
        $group->teachers()->sync($teachersData);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Группа успешно обновлена.');
    }

    /**
     * Remove the specified group.
     */
    public function destroy(Group $group)
    {
        // Check if group has schedules
        if ($group->schedules()->exists()) {
            return back()->with('error', 'Нельзя удалить группу с расписанием. Сначала удалите все занятия.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Группа успешно удалена.');
    }

    /**
     * Sync students from external API.
     */
    public function syncStudents(Group $group)
    {
        try {
            $students = $this->apiService->getStudentsByGroup($group->code);
            
            foreach ($students as $studentData) {
                $group->students()->updateOrCreate(
                    ['external_id' => $studentData['id']],
                    [
                        'first_name' => $studentData['first_name'],
                        'last_name' => $studentData['last_name'],
                        'middle_name' => $studentData['middle_name'] ?? null,
                        'email' => $studentData['email'] ?? null,
                        'phone' => $studentData['phone'] ?? null,
                        'birth_date' => $studentData['birth_date'] ?? null,
                        'student_card_number' => $studentData['student_card_number'] ?? null,
                        'additional_data' => $studentData,
                        'synced_at' => now(),
                    ]
                );
            }

            return back()->with('success', 'Список студентов успешно синхронизирован.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка синхронизации: ' . $e->getMessage());
        }
    }
}