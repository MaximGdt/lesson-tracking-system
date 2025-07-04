@extends('layouts.app')

@section('title', 'Редактирование занятия')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Редактирование занятия</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
            @csrf
            @method('PUT')
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Основная информация</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_id" class="form-label">Группа *</label>
                            <select class="form-select @error('group_id') is-invalid @enderror" 
                                    id="group_id" 
                                    name="group_id" 
                                    required>
                                <option value="">Выберите группу</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" 
                                            {{ old('group_id', $schedule->group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->code }} - {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Преподаватель *</label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                    id="teacher_id" 
                                    name="teacher_id" 
                                    required>
                                <option value="">Выберите преподавателя</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                            {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="subject" class="form-label">Предмет *</label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject', $schedule->subject) }}" 
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Тип занятия *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="lecture" {{ old('type', $schedule->type) == 'lecture' ? 'selected' : '' }}>
                                    Лекция
                                </option>
                                <option value="practice" {{ old('type', $schedule->type) == 'practice' ? 'selected' : '' }}>
                                    Практика
                                </option>
                                <option value="lab" {{ old('type', $schedule->type) == 'lab' ? 'selected' : '' }}>
                                    Лабораторная
                                </option>
                                <option value="exam" {{ old('type', $schedule->type) == 'exam' ? 'selected' : '' }}>
                                    Экзамен
                                </option>
                                <option value="consultation" {{ old('type', $schedule->type) == 'consultation' ? 'selected' : '' }}>
                                    Консультация
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Дата и время</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Дата *</label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', $schedule->date->format('Y-m-d')) }}" 
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="start_time" class="form-label">Время начала *</label>
                            <input type="time" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time', $schedule->start_time->format('H:i')) }}" 
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="end_time" class="form-label">Время окончания *</label>
                            <input type="time" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="{{ old('end_time', $schedule->end_time->format('H:i')) }}" 
                                   required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="room" class="form-label">Аудитория</label>
                            <input type="text" 
                                   class="form-control @error('room') is-invalid @enderror" 
                                   id="room" 
                                   name="room" 
                                   value="{{ old('room', $schedule->room) }}">
                            @error('room')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    @if($schedule->isPast() || $schedule->is_cancelled)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            @if($schedule->is_cancelled)
                                Это занятие отменено. Вы редактируете отмененное занятие.
                            @elseif($schedule->isPast())
                                Это занятие уже прошло. Изменение даты/времени может повлиять на отчеты.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Дополнительно</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Примечания</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3">{{ old('notes', $schedule->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary me-2">
                    Отмена
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Сохранить изменения
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Информация о занятии</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>Текущий статус:</dt>
                    <dd>
                        @if($schedule->is_cancelled)
                            <span class="badge bg-secondary">Отменено</span>
                        @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                            <span class="badge bg-success">Проведено</span>
                        @elseif($schedule->isPast())
                            <span class="badge bg-warning">Не отмечено</span>
                        @else
                            <span class="badge bg-light text-dark">Запланировано</span>
                        @endif
                    </dd>
                    
                    <dt>Создано:</dt>
                    <dd>{{ $schedule->created_at->format('d.m.Y H:i') }}</dd>
                    
                    <dt>Последнее обновление:</dt>
                    <dd>{{ $schedule->updated_at->format('d.m.Y H:i') }}</dd>
                    
                    @if($schedule->lesson)
                        <dt>Отметка о проведении:</dt>
                        <dd>
                            @if($schedule->lesson->is_conducted)
                                {{ $schedule->lesson->marked_at->format('d.m.Y H:i') }}<br>
                                <small>{{ $schedule->lesson->markedBy->full_name }}</small>
                            @else
                                Не отмечено
                            @endif
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Предупреждения</h5>
            </div>
            <div class="card-body">
                @if($schedule->lesson && $schedule->lesson->is_conducted)
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Занятие отмечено как проведенное. Изменение основных параметров может повлиять на отчеты.
                    </div>
                @else
                    <p class="text-muted mb-0">
                        <small>После сохранения изменений все связанные пользователи будут уведомлены об изменениях в расписании.</small>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection