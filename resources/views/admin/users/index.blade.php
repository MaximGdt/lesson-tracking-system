@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{__('app.users')}}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> {{__('app.add_user')}}
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{__('app.search')}}</label>
                <input type="text" name="search" class="form-control" placeholder="{{__('app.search_bio')}}"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{__('app.role')}}</label>
                <select name="role" class="form-select">
                    <option value="">{{__('app.all_roles')}}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{__('app'.$role->name)}}
                        </option>
                    @endforeach
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Поиск
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Сброс
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роли</th>
                    <th>Статус</th>
                    <th>Последний вход</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary badge-role">{{ $role->display_name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-danger">Неактивен</span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <small>{{ $user->last_login_at->format('d.m.Y H:i') }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-info btn-action"
                                   title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-primary btn-action"
                                   title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    <form method="POST"
                                          action="{{ route('admin.users.toggle-status', $user) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-warning btn-action"
                                                title="{{ $user->is_active ? 'Деактивировать' : 'Активировать' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'lock' : 'unlock' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Вы уверены, что хотите удалить этого пользователя?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-action"
                                                title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Пользователи не найдены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-3">
    {{ $users->withQueryString()->links() }}
</div>
@endsection
