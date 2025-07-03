@extends('layouts.app')

@section('title', 'Мое расписание')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Мое расписание</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.schedule.calendar') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar3"></i> Календарь
            </a>
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Печать
        </button>
    </div>
</div>

<!-- Date Navigation -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.schedule') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">С даты</label>
                <input type="date" name="start_date" class="form-control" 
                       value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">По дату</label>
                <input type="date" name="end_date" class="form-control" 
                       value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Показать
                </button>
                <a href="{{ route('teacher.schedule') }}" class="btn btn-secondary">
                    Текущая неделя
                </a>
            </div>
            <div class="col-md-2 text-end">
                <div class="btn-group">
                    <a href="{{ route('teacher.schedule', ['start_date' => $startDate->copy()->subWeek()->format('Y-m-d'), 'end_date' => $endDate->copy()->subWeek()->format('Y-m-d')]) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a href="{{ route('teacher.schedule', ['start_date' => $startDate->copy()->addWeek()->format('Y-m-d'), 'end_date' => $endDate->copy()->addWeek()->format('Y-m-d')]) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Schedule Display -->
@if($schedules->isEmpty())
    <div class="alert alert-info text-center">
        <i class="bi bi-calendar-x"></i> На выбранный период занятий нет
    </div>
@else
    @foreach($schedules as $date => $daySchedules)
        <div class="card mb-3">
            <div class="card-header {{ Carbon\Carbon::parse($date)->isToday() ? 'bg-primary text-white' : '' }}">
                <h5 class="mb-0">
                    {{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    @if(Carbon\Carbon::parse($date)->isToday())
                        <span class="badge bg-white text-primary ms-2">Сегодня</span>
                    @endif
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="15%">Время</th>
                                <th width="15%">Группа</th>
                                <th width="30%">Предмет</th>
                                <th width="10%">Тип</th>
                                <th width="10%">Аудитория</th>
                                <th width="15%">Статус</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daySchedules as $schedule)
                                <tr class="{{ $schedule->is_cancelled ? 'table-secondary' : '' }}">
                                    <td>
                                        <strong>{{ $schedule->start_time->format('H:i') }}</strong> - 
                                        {{ $schedule->end_time->format('H:i') }}
                                    </td>
                                    <td>
                                        <strong>{{ $schedule->group->code }}</strong><br>
                                        <small class="text-muted">{{ $schedule->group->name }}</small>
                                    </td>
                                    <td>{{ $schedule->subject }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $schedule->type_display }}</span>
                                    </td>
                                    <td>{{ $schedule->room ?? '-' }}</td>
                                    <td>
                                        @if($schedule->is_cancelled)
                                            <span class="badge bg-secondary">Отменено</span>
                                        @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Проведено
                                            </span>
                                        @elseif($schedule->isPast())
                                            <span class="badge bg-warning">Не отмечено</span>
                                        @else
                                            <span class="badge bg-light text-dark">Запланировано</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.lessons.show', $schedule) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Summary -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $schedules->flatten()->count() }}</h3>
                <p class="mb-0">Всего занятий</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">
                    {{ $schedules->flatten()->filter(function($s) { 
                        return $s->lesson && $s->lesson->is_conducted; 
                    })->count() }}
                </h3>
                <p class="mb-0">Проведено</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ $groups->count() }}</h3>
                <p class="mb-0">Групп</p>
            </div>
        </div>
    </div>
</div>
@endsection