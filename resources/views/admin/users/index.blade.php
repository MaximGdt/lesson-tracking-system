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
                <label class="form-label">{{__('app.status')}}</label>
                <select name="status" class="form-select">
                    <option value="">{{__('app.all')}}</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{__('app.active')}}</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{__('app.no_active')}}</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> {{__('app.search')}}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i> {{__('app.reset')}}
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
                    <th>{{__('app.id')}}</th>
                    <th>{{__('full_name')}}</th>
                    <th>{{__('app.email')}}</th>
                    <th>{{__('app.phone')}}</th>
                    <th>{{__('app.roles')}}</th>
                    <th>{{__('app.roles')}}</th>
                    <th>{{__('app.last_login')}}</th>
                    <th>{{__('app.actions')}}</th>
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
                                @if($role->id == 1)
                                    <span class="badge bg-primary badge-role">{{__('app.role_super_admin')}}</span>
                                @elseif($role->id == 2)
                                    <span class="badge bg-primary badge-role">{{__('app.role_admin')}}</span>
                                @elseif($role->id == 3)
                                    <span class="badge bg-primary badge-role">{{__('app.role_teacher')}}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">{{__('app.active')}}</span>
                            @else
                                <span class="badge bg-danger">{{__('app.no_active')}}</span>
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
                                   title="{{__('app.view')}}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-primary btn-action"
                                   title="{{__('app.edit')}}">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    <form method="POST"
                                          action="{{ route('admin.users.toggle-status', $user) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-warning btn-action"
                                                title="{{ $user->is_active ? __('app.deactivate') : __('app.activate') }}">
                                            <i class="bi bi-{{ $user->is_active ? 'lock' : 'unlock' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('{{__('app.confirm_delete')}}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-action"
                                                title="{{__('app.delete')}}">
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
                            {{__('app.users_not_found')}}
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
