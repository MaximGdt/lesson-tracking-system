@extends('layouts.app')

@section('title', 'Мои занятия')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Мои занятия</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('teacher.lessons.today') }}" class="btn btn-primary">
            <i class="bi bi-calendar-day"></i> Занятия на сегодня
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.lessons.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Дата от</label>
                <input type="date" name="date_from" class="form-control" 
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Дата до</label>
                <input type="date" name="date_to" class="form-control" 
                       value="{{ request('date_to') }}">
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
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <option value="conducted" {{ request('status') == 'conducted' ? 'selected' : '' }}>
                        Проведенные
                    </option>
                    <option value="not_conducted" {{ request('status') == 'not_conducted' ? 'selected' : '' }}>
                        Не проведенные
                    </option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Поиск
                </button>
                <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Сброс
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lessons Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Группа</th>
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
                        <td>{{ $schedule->subject }}</td>
                        <td>
                            <span class="badge bg-info">{{ $schedule->type_display }}</span>
                        </td>
                        <td>{{ $schedule->room ?? '-' }}</td>
                        <td>
                            @if($schedule->is_cancelled)
                                <span class="badge bg-secondary">Отменено</span>
                            @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                                <span class="badge bg-success">Проведено</span>
                                <br>
                                <small class="text-muted">
                                    {{ $schedule->lesson->marked_at->format('d.m H:i') }}
                                </small>
                            @elseif($schedule->isPast())
                                <span class="badge bg-warning">Не отмечено</span>
                            @else
                                <span class="badge bg-light text-dark">Запланировано</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('teacher.lessons.show', $schedule) }}" 
                                   class="btn btn-sm btn-info btn-action" 
                                   title="Подробнее">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(!$schedule->is_cancelled)
                                    @if(!$schedule->lesson || !$schedule->lesson->is_conducted)
                                        <button type="button" 
                                                class="btn btn-sm btn-success btn-action" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#markModal{{ $schedule->id }}"
                                                title="Отметить проведенным"
                                                {{ $schedule->isFuture() && !auth()->user()->isSuperAdmin() ? 'disabled' : '' }}>
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    @else
                                        @can('mark-lesson', $schedule)
                                            <form method="POST" 
                                                  action="{{ route('teacher.lessons.mark-not-conducted', $schedule) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-warning btn-action" 
                                                        title="Снять отметку"
                                                        onclick="return confirm('Снять отметку о проведении?');">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Mark Modal -->
                    @if(!$schedule->is_cancelled && (!$schedule->lesson || !$schedule->lesson->is_conducted))
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
                                            <div class="mb-3">
                                                <label class="form-label">Количество присутствующих студентов</label>
                                                <input type="number" 
                                                       name="students_present" 
                                                       class="form-control" 
                                                       min="0" 
                                                       max="{{ $schedule->group->students->count() }}"
                                                       placeholder="Необязательно">
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
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Занятия не найдены
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
@endsection