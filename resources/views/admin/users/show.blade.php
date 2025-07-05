@extends('layouts.app')

@section('title', 'Просмотр пользователя')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $user->full_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> {{__('app.edit')}}
            </a>
            @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                        <i class="bi bi-{{ $user->is_active ? 'lock' : 'unlock' }}"></i>
                        {{ $user->is_active ? 'Деактивировать' : 'Активировать' }}
                    </button>
                </form>
            @endif
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- User Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{__('app.personal_info')}}</h5>
            </div>
            <div class="card-body">
                @if($user->avatar)
                    <div class="text-center mb-3">
                        <img src="{{ Storage::url($user->avatar) }}"
                             alt="Avatar"
                             class="rounded-circle"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                @endif

                <dl class="mb-0">
                    <dt>{{__('app.full_name')}}:</dt>
                    <dd>{{ $user->full_name }}</dd>

                    <dt>Email:</dt>
                    <dd>
                        {{ $user->email }}
                        @if($user->email_verified_at)
                            <i class="bi bi-check-circle text-success" title="Подтвержден"></i>
                        @else
                            <i class="bi bi-x-circle text-danger" title="Не подтвержден"></i>
                        @endif
                    </dd>

                    <dt>{{__('app.phone')}}:</dt>
                    <dd>{{ $user->phone ?? '-' }}</dd>

                    <dt>{{__('app.interface_language')}}:</dt>
                    <dd>{{ $user->locale == 'uk' ? __('app.ukrainian') : __('app.english') }}</dd>

                    <dt>{{__('app.status')}}:</dt>
                    <dd>
                        @if($user->is_active)
                            <span class="badge bg-success">{{__('app.active')}}</span>
                        @else
                            <span class="badge bg-danger">{{__('app.no_active')}}</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Roles -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{__('app.role')}}</h5>
            </div>
            <div class="card-body">
                @foreach($user->roles as $role)
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ $role->display_name }}</span>
                        @if($role->description)
                            <br><small class="text-muted">{{ $role->description }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Activity -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{__('app.activity')}}</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>{{__('app.register_at')}}:</dt>
                    <dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd>

                    <dt>{{__('app.last_activity')}}:</dt>
                    <dd>
                        @if($user->last_login_at)
                            {{ $user->last_login_at->format('d.m.Y H:i') }}
                            <br>
                            <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                        @else
                            <span class="text-muted">{{__('app.dont_enter')}}</span>
                        @endif
                    </dd>

                    <dt>{{__('app.last_update')}}:</dt>
                    <dd>{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($user->isTeacher())
            <!-- Teacher Stats -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{__('app.teacher_statistics')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $user->groups->count() }}</h3>
                            <p class="text-muted">{{__('app.group')}}</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info">
                                {{ $user->schedules()->where('date', '>=', today())->count() }}
                            </h3>
                            <p class="text-muted">{{__('app.planed')}}</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success">
                                {{ $user->schedules()
                                    ->whereMonth('date', now()->month)
                                    ->whereHas('lesson', function($q) {
                                        $q->where('is_conducted', true);
                                    })->count() }}
                            </h3>
                            <p class="text-muted">{{__('app.conducted_in_current_month')}}</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning">
                                {{ $user->markedLessons()->count() }}
                            </h3>
                            <p class="text-muted">{{__('app.total_conducted')}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{__('app.teacher_groups')}}</h5>
                </div>
                <div class="card-body">
                    @if($user->groups->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{__('app.group_code')}}</th>
                                        <th>{{__('app.group_name')}}</th>
                                        <th>{{__('app.group_subject')}}</th>
                                        <th>{{__('app.course')}}</th>
                                        <th>{{__('app.students')}}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->groups as $group)
                                        <tr>
                                            <td><strong>{{ $group->code }}</strong></td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->pivot->subject }}</td>
                                            <td>{{ $group->course }}</td>
                                            <td>{{ $group->students->count() }}</td>
                                            <td>
                                                <a href="{{ route('admin.groups.show', $group) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{__('app.no_groups')}}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Recent Schedules -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{__('app.recently_lessons')}}</h5>
            </div>
            <div class="card-body">
                @if($user->schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{__('app.date')}}</th>
                                    <th>{{__('app.time')}}</th>
                                    <th>{{__('app.group')}}</th>
                                    <th>{{__('app.group_subject')}}</th>
                                    <th>{{__('app.status')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->date->format('d.m.Y') }}</td>
                                        <td>{{ $schedule->time_range }}</td>
                                        <td>{{ $schedule->group->code }}</td>
                                        <td>{{ $schedule->subject }}</td>
                                        <td>
                                            @if($schedule->is_cancelled)
                                                <span class="badge bg-secondary">{{__('app.canceled')}}</span>
                                            @elseif($schedule->lesson && $schedule->lesson->is_conducted)
                                                <span class="badge bg-success">{{__('app.conducted')}}</span>
                                            @elseif($schedule->isPast())
                                                <span class="badge bg-warning">{{__('app.not_marked')}}</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{__('app.planed')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.schedules.show', $schedule) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Нет занятий</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
