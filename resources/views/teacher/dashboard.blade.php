@extends('layouts.app')

@section('title', 'Панель преподавателя')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Добро пожаловать, {{ auth()->user()->first_name }}!</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('teacher.lessons.today') }}" class="btn btn-primary">
            <i class="bi bi-calendar-check"></i> Отметить занятия
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Занятий сегодня
                        </div>
                        <div class="h5 mb-0 fw-bold">{{ $todaySchedules->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-day fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Проведено за неделю
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            {{ $weekStats['conducted'] }} / {{ $weekStats['total'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Проведено за месяц
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            {{ $monthStats['conducted'] }} / {{ $monthStats['total'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-month fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Мои группы
                        </div>
                        <div class="h5 mb-0 fw-bold">{{ $groups->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-calendar-day"></i> Расписание на сегодня
                </h6>
            </div>
            <div class="card-body">
                @if($todaySchedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Время</th>
                                    <th>Группа</th>
                                    <th>Предмет</th>
                                    <th>Аудитория</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->time_range }}</td>
                                        <td>{{ $schedule->group->code }}</td>
                                        <td>{{ $schedule->subject }}</td>
                                        <td>{{ $schedule->room ?? '-' }}</td>
                                        <td>
                                            @if($schedule->lesson && $schedule->lesson->is_conducted)
                                                <span class="badge bg-success">Проведено</span>
                                            @else
                                                <span class="badge bg-warning">Ожидает отметки</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('teacher.lessons.show', $schedule) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">На сегодня занятий нет</p>
                @endif
            </div>
        </div>
    </div>

    <!-- My Groups -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-people"></i> Мои группы
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($groups as $group)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $group->code }}</h6>
                                    <small class="text-muted">{{ $group->name }}</small>
                                    <br>
                                    <small class="text-muted">
                                        Предмет: {{ $group->pivot->subject }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-info">
                                        {{ $group->students_count }} студ.
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Upcoming -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-clock-history"></i> Последние отмеченные занятия
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Группа</th>
                                <th>Предмет</th>
                                <th>Отмечено</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->schedule->date->format('d.m') }}</td>
                                    <td>{{ $lesson->schedule->group->code }}</td>
                                    <td>{{ Str::limit($lesson->schedule->subject, 20) }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $lesson->marked_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Нет отмеченных занятий
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-calendar-plus"></i> Ближайшие занятия
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Группа</th>
                                <th>Предмет</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingSchedules as $schedule)
                                <tr>
                                    <td>
                                        {{ $schedule->date->format('d.m') }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $schedule->date->translatedFormat('D') }}
                                        </small>
                                    </td>
                                    <td>{{ $schedule->start_time->format('H:i') }}</td>
                                    <td>{{ $schedule->group->code }}</td>
                                    <td>{{ Str::limit($schedule->subject, 20) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Нет запланированных занятий
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection