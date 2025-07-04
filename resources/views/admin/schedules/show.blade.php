@extends('layouts.app')

@section('title', 'Просмотр расписания')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Детали занятия</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Редактировать
            </a>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Schedule Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Информация о занятии</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt>Дата и время:</dt>
                            <dd>
                                {{ $schedule->date->format('d.m.Y') }} 
                                ({{ $schedule->date->translatedFormat('l') }})<br>
                                {{ $schedule->time_range }}
                            </dd>
                            
                            <dt>Группа:</dt>
                            <dd>
                                <strong>{{ $schedule->group->code }}</strong><br>
                                {{ $schedule->group->name }}
                            </dd>
                            
                            <dt>Преподаватель:</dt>
                            <dd>
                                {{ $schedule->teacher->full_name }}<br>
                                <small class="text-muted">{{ $schedule->teacher->email }}</small>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt>Предмет:</dt>
                            <dd>{{ $schedule->subject }}</dd>
                            
                            <dt>Тип занятия:</dt>
                            <dd><span class="badge bg-info">{{ $schedule->type_display }}</span></dd>
                            
                            <dt>Аудитория:</dt>
                            <dd>{{ $schedule->room ?? '-' }}</dd>
                            
                            <dt>Статус:</dt>
                            <dd>
                                @if($schedule->is_cancelled)
                                    <span class="badge bg-secondary">Отменено</span>
                                    <br><small>{{ $schedule->cancellation_reason }}</small>
                                @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                                    <span class="badge bg-success">Проведено</span>
                                @elseif($schedule->isPast())
                                    <span class="badge bg-warning">Не отмечено</span>
                                @else
                                    <span class="badge bg-light text-dark">Запланировано</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
                
                @if($schedule->notes)
                    <hr>
                    <p class="mb-0"><strong>Примечания к расписанию:</strong><br>
                    {{ $schedule->notes }}</p>
                @endif
            </div>
        </div>
        
        <!-- Lesson Details -->
        @if($schedule->lesson)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Детали проведения</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Статус проведения:</dt>
                                <dd>
                                    @if($schedule->lesson->is_conducted)
                                        <span class="badge bg-success">Проведено</span>
                                    @else
                                        <span class="badge bg-secondary">Не проведено</span>
                                    @endif
                                </dd>
                                
                                <dt>Отмечено:</dt>
                                <dd>
                                    @if($schedule->lesson->marked_at)
                                        {{ $schedule->lesson->marked_at->format('d.m.Y H:i') }}<br>
                                        <small class="text-muted">{{ $schedule->lesson->markedBy->full_name }}</small>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Присутствовало студентов:</dt>
                                <dd>
                                    @if($schedule->lesson->students_present !== null)
                                        {{ $schedule->lesson->students_present }} из {{ $schedule->group->students->count() }}
                                        @if($schedule->lesson->attendance_percentage)
                                            <span class="badge bg-info">{{ $schedule->lesson->attendance_percentage }}%</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($schedule->lesson->notes)
                        <hr>
                        <p class="mb-0"><strong>Примечания к занятию:</strong><br>
                        {{ $schedule->lesson->notes }}</p>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    @if(!$schedule->is_cancelled && (!$schedule->lesson || !$schedule->lesson->is_conducted))
                        <button type="button" 
                                class="btn btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle"></i> Отменить занятие
                        </button>
                        
                        <form method="POST" 
                              action="{{ route('admin.schedules.destroy', $schedule) }}" 
                              onsubmit="return confirm('Удалить занятие из расписания?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Удалить
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection