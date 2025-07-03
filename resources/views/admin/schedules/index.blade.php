@extends('layouts.app')

@section('title', 'Управление расписанием')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Расписание</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkCreateModal">
                <i class="bi bi-calendar-plus"></i> Массовое создание
            </button>
        </div>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Добавить занятие
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Дата</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Группа</label>
                <select name="group_id" class="form-select">
                    <option value="">Все группы</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->code }} - {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Преподаватель</label>
                <select name="teacher_id" class="form-select">
                    <option value="">Все преподаватели</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Schedules Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Группа</th>
                    <th>Преподаватель</th>
                    <th>Предмет</th>
                    <th>Тип</th>
                    <th>Аудитория</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr class="{{ $schedule->is_cancelled ? 'table-secondary' : '' }}">
                        <td>
                            {{ $schedule->date->format('d.m.Y') }}
                            <br>
                            <small class="text-muted">{{ $schedule->date->translatedFormat('l') }}</small>
                        </td>
                        <td>{{ $schedule->time_range }}</td>
                        <td>
                            <strong>{{ $schedule->group->code }}</strong>
                            <br>
                            <small>{{ $schedule->group->name }}</small>
                        </td>
                        <td>{{ $schedule->teacher->short_name }}</td>
                        <td>{{ $schedule->subject }}</td>
                        <td>
                            <span class="badge bg-info">{{ $schedule->type_display }}</span>
                        </td>
                        <td>{{ $schedule->room ?? '-' }}</td>
                        <td>
                            @if($schedule->is_cancelled)
                                <span class="badge bg-secondary">Отменено</span>
                                <br>
                                <small class="text-muted">{{ $schedule->cancellation_reason }}</small>
                            @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                                <span class="badge bg-success">Проведено</span>
                            @elseif($schedule->isPast())
                                <span class="badge bg-warning">Не отмечено</span>
                            @else
                                <span class="badge bg-light text-dark">Запланировано</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.schedules.show', $schedule) }}" 
                                   class="btn btn-sm btn-info btn-action">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" 
                                   class="btn btn-sm btn-primary btn-action">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                @if(!$schedule->is_cancelled && !($schedule->lesson && $schedule->lesson->is_conducted))
                                    <button type="button" 
                                            class="btn btn-sm btn-warning btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $schedule->id }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    
                                    <form method="POST" 
                                          action="{{ route('admin.schedules.destroy', $schedule) }}" 
                                          class="d-inline"
                                          onsubmit="return confirm('Удалить занятие?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-action">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Cancel Modal -->
                    <div class="modal fade" id="cancelModal{{ $schedule->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.schedules.cancel', $schedule) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Отмена занятия</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Причина отмены *</label>
                                            <input type="text" name="cancellation_reason" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Закрыть
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-x-circle"></i> Отменить занятие
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            Расписание не найдено
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-3">
    {{ $schedules->withQueryString()->links() }}
</div>

<!-- Bulk Create Modal -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.schedules.bulk-create') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Массовое создание расписания</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Группа *</label>
                            <select name="group_id" class="form-select" required>
                                <option value="">Выберите группу</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->code }} - {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Преподаватель *</label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Выберите преподавателя</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Предмет *</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Тип занятия *</label>
                            <select name="type" class="form-select" required>
                                <option value="lecture">Лекция</option>
                                <option value="practice">Практика</option>
                                <option value="lab">Лабораторная</option>
                                <option value="consultation">Консультация</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Аудитория</label>
                            <input type="text" name="room" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">День недели *</label>
                            <select name="day_of_week" class="form-select" required>
                                <option value="1">Понедельник</option>
                                <option value="2">Вторник</option>
                                <option value="3">Среда</option>
                                <option value="4">Четверг</option>
                                <option value="5">Пятница</option>
                                <option value="6">Суббота</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Начало *</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Конец *</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Дата начала *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Дата окончания *</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check">
                                <input type="checkbox" name="skip_holidays" value="1" checked class="form-check-input">
                                <label class="form-check-label">Пропускать праздники</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus"></i> Создать расписание
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection