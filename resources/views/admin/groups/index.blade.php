@extends('layouts.app')

@section('title', 'Управление группами')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Группы</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Добавить группу
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.groups.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Поиск</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Название или код группы" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Курс</label>
                <select name="course" class="form-select">
                    <option value="">Все курсы</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>
                            {{ $i }} курс
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Активные</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Неактивные</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Поиск
                </button>
                <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Сброс
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Groups Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Код</th>
                    <th>Название</th>
                    <th>Курс</th>
                    <th>Специальность</th>
                    <th>Студентов</th>
                    <th>Преподавателей</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                    <tr>
                        <td><strong>{{ $group->code }}</strong></td>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->course }}</td>
                        <td>{{ $group->speciality ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $group->students_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $group->teachers_count }}</span>
                        </td>
                        <td>
                            @if($group->is_active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-danger">Неактивна</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.groups.show', $group) }}" 
                                   class="btn btn-sm btn-info btn-action" 
                                   title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.groups.edit', $group) }}" 
                                   class="btn btn-sm btn-primary btn-action" 
                                   title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.groups.sync-students', $group) }}" 
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm btn-success btn-action" 
                                            title="Синхронизировать студентов">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form method="POST" 
                                      action="{{ route('admin.groups.destroy', $group) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('Вы уверены? Все связанные данные будут удалены!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger btn-action" 
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Группы не найдены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-3">
    {{ $groups->withQueryString()->links() }}
</div>
@endsection