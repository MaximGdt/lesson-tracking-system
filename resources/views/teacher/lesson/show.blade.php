@extends('layouts.app')

@section('title', 'Детали занятия')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Детали занятия</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
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
                            
                            <dt>Предмет:</dt>
                            <dd>{{ $schedule->subject }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
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
            
            <!-- Actions -->
            @if(!$schedule->is_cancelled)
                <div class="card">
                    <div class="card-body">
                        @if($schedule->lesson->is_conducted)
                            <form method="POST" action="{{ route('teacher.lessons.mark-not-conducted', $schedule) }}" 
                                  onsubmit="return confirm('Снять отметку о проведении занятия?');">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-x-circle"></i> Снять отметку о проведении
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markModal">
                                <i class="bi bi-check-circle"></i> Отметить как проведенное
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <!-- Mark as Conducted -->
            @if(!$schedule->is_cancelled && !$schedule->isFuture())
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-3">Занятие еще не отмечено</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markModal">
                            <i class="bi bi-check-circle"></i> Отметить как проведенное
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Group Students -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Студенты группы ({{ $schedule->group->students->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($schedule->group->students->sortBy('last_name') as $index => $student)
                        <div class="list-group-item px-0 py-1">
                            <small>{{ $index + 1 }}. {{ $student->full_name }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- History -->
        @if($schedule->lesson && $schedule->lesson->logs->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">История изменений</h5>
                </div>
                <div class="card-body">
                    @foreach($schedule->lesson->logs as $log)
                        <div class="mb-2">
                            <small class="text-muted">
                                {{ $log->created_at->format('d.m.Y H:i') }}<br>
                                <strong>{{ $log->user->short_name }}</strong>: {{ $log->action_display }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Mark Modal -->
<div class="modal fade" id="markModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.lessons.mark-conducted', $schedule) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Отметить занятие как проведенное</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                        <label class="form-label">Примечания</label>
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
@endsection