@extends('layouts.app')

@section('title', 'Занятия на сегодня')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Занятия на сегодня - {{ now()->translatedFormat('d F Y') }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
            <i class="bi bi-list"></i> Все занятия
        </a>
    </div>
</div>

@if($schedules->isEmpty())
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle fs-1"></i>
        <h4 class="mt-3">На сегодня занятий нет</h4>
        <p>Отдыхайте или подготовьтесь к следующим занятиям.</p>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house"></i> На главную
        </a>
    </div>
@else
    <div class="row">
        @foreach($schedules as $schedule)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 {{ $schedule->lesson && $schedule->lesson->is_conducted ? 'border-success' : '' }}">
                    <div class="card-header {{ $schedule->lesson && $schedule->lesson->is_conducted ? 'bg-success text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="bi bi-clock"></i> {{ $schedule->time_range }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $schedule->subject }}</h6>
                        <p class="card-text">
                            <strong>Группа:</strong> {{ $schedule->group->code }}<br>
                            <small class="text-muted">{{ $schedule->group->name }}</small>
                        </p>
                        <p class="card-text">
                            <strong>Тип:</strong> <span class="badge bg-info">{{ $schedule->type_display }}</span><br>
                            @if($schedule->room)
                                <strong>Аудитория:</strong> {{ $schedule->room }}
                            @endif
                        </p>
                        
                        @if($schedule->lesson && $schedule->lesson->is_conducted)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle"></i> Проведено
                                <br>
                                <small>{{ $schedule->lesson->marked_at->format('H:i') }}</small>
                                @if($schedule->lesson->students_present)
                                    <br>
                                    <small>Присутствовало: {{ $schedule->lesson->students_present }} студентов</small>
                                @endif
                            </div>
                        @else
                            <button type="button" 
                                    class="btn btn-success w-100" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#markModal{{ $schedule->id }}">
                                <i class="bi bi-check-circle"></i> Отметить как проведенное
                            </button>
                        @endif
                    </div>
                    <div class="card-footer text-muted">
                        <small>
                            <i class="bi bi-people"></i> {{ $schedule->group->students->count() }} студентов в группе
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Mark Modal -->
            <div class="modal fade" id="markModal{{ $schedule->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('teacher.lessons.mark-conducted', $schedule) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Отметить занятие как проведенное</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>{{ $schedule->subject }}</strong><br>
                                    Группа: {{ $schedule->group->code }}<br>
                                    Время: {{ $schedule->time_range }}
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Количество присутствующих студентов</label>
                                    <input type="number" 
                                           name="students_present" 
                                           class="form-control" 
                                           min="0" 
                                           max="{{ $schedule->group->students->count() }}"
                                           value="{{ $schedule->group->students->count() }}">
                                    <small class="text-muted">
                                        Всего в группе: {{ $schedule->group->students->count() }} студентов
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Примечания к занятию</label>
                                    <textarea name="notes" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Необязательно"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Отмена
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Отметить проведенным
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Summary -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Итого на сегодня:</h5>
            <div class="row text-center">
                <div class="col-md-4">
                    <h3 class="text-primary">{{ $schedules->count() }}</h3>
                    <p class="text-muted">Всего занятий</p>
                </div>
                <div class="col-md-4">
                    <h3 class="text-success">
                        {{ $schedules->filter(function($s) { return $s->lesson && $s->lesson->is_conducted; })->count() }}
                    </h3>
                    <p class="text-muted">Проведено</p>
                </div>
                <div class="col-md-4">
                    <h3 class="text-warning">
                        {{ $schedules->filter(function($s) { return !$s->lesson || !$s->lesson->is_conducted; })->count() }}
                    </h3>
                    <p class="text-muted">Осталось</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection