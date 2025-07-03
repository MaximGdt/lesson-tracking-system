@extends('layouts.app')

@section('title', 'Отчеты')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Отчеты</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.reports.generate') }}" target="_blank">
            @csrf
            
            <!-- Report Type Selection -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Выберите тип отчета</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="report_type" id="lessons_report" value="lessons" checked>
                        <label class="form-check-label" for="lessons_report">
                            <strong>Отчет по проведенным занятиям</strong>
                            <br>
                            <small class="text-muted">Детальная информация о всех проведенных занятиях за период</small>
                        </label>
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="report_type" id="teacher_workload" value="teacher_workload">
                        <label class="form-check-label" for="teacher_workload">
                            <strong>Нагрузка преподавателей</strong>
                            <br>
                            <small class="text-muted">Статистика по количеству занятий и часов каждого преподавателя</small>
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report_type" id="group_attendance" value="group_attendance">
                        <label class="form-check-label" for="group_attendance">
                            <strong>Посещаемость по группам</strong>
                            <br>
                            <small class="text-muted">Анализ посещаемости студентов по группам</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Фильтры</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Дата от</label>
                            <input type="date" 
                                   name="date_from" 
                                   class="form-control" 
                                   value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Дата до</label>
                            <input type="date" 
                                   name="date_to" 
                                   class="form-control" 
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Преподаватель</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">Все преподаватели</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Группа</label>
                            <select name="group_id" class="form-select">
                                <option value="">Все группы</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->code }} - {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Export Format -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Формат вывода</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="format" id="format_view" value="view" checked>
                        <label class="btn btn-outline-primary" for="format_view">
                            <i class="bi bi-eye"></i> Просмотр
                        </label>
                        
                        <input type="radio" class="btn-check" name="format" id="format_excel" value="excel">
                        <label class="btn btn-outline-success" for="format_excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </label>
                        
                        <input type="radio" class="btn-check" name="format" id="format_pdf" value="pdf">
                        <label class="btn btn-outline-danger" for="format_pdf">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-file-earmark-bar-graph"></i> Сформировать отчет
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Быстрая статистика</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Занятий сегодня:</span>
                    <strong>{{ \App\Models\Schedule::whereDate('date', today())->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Проведено за неделю:</span>
                    <strong>
                        {{ \App\Models\Lesson::whereHas('schedule', function($q) {
                            $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                        })->where('is_conducted', true)->count() }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Проведено за месяц:</span>
                    <strong>
                        {{ \App\Models\Lesson::whereHas('schedule', function($q) {
                            $q->whereMonth('date', now()->month)->whereYear('date', now()->year);
                        })->where('is_conducted', true)->count() }}
                    </strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Активных групп:</span>
                    <strong>{{ \App\Models\Group::where('is_active', true)->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Преподавателей:</span>
                    <strong>{{ \App\Models\User::whereHas('roles', function($q) {
                        $q->where('name', 'teacher');
                    })->count() }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Help -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Справка</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Отчет по занятиям</strong> - содержит детальную информацию о каждом проведенном занятии.</p>
                <p class="mb-2"><strong>Нагрузка преподавателей</strong> - показывает количество запланированных и проведенных занятий для каждого преподавателя.</p>
                <p class="mb-2"><strong>Посещаемость</strong> - анализирует среднюю посещаемость студентов по группам.</p>
                <hr>
                <p class="mb-0"><small>Отчеты можно экспортировать в Excel для дальнейшей обработки или в PDF для печати.</small></p>
            </div>
        </div>
    </div>
</div>
@endsection