@extends('layouts.app')

@section('title', 'Просмотр группы')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $group->code }} - {{ $group->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Редактировать
            </a>
            <form method="POST" action="{{ route('admin.groups.sync-students', $group) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-arrow-repeat"></i> Синхронизировать студентов
                </button>
            </form>
        </div>
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <!-- Group Info -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Информация о группе</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Код:</dt>
                    <dd class="col-sm-7"><strong>{{ $group->code }}</strong></dd>
                    
                    <dt class="col-sm-5">Название:</dt>
                    <dd class="col-sm-7">{{ $group->name }}</dd>
                    
                    <dt class="col-sm-5">Курс:</dt>
                    <dd class="col-sm-7">{{ $group->course }}</dd>
                    
                    <dt class="col-sm-5">Специальность:</dt>
                    <dd class="col-sm-7">{{ $group->speciality ?? '-' }}</dd>
                    
                    <dt class="col-sm-5">Начало обучения:</dt>
                    <dd class="col-sm-7">{{ $group->start_date ? $group->start_date->format('d.m.Y') : '-' }}</dd>
                    
                    <dt class="col-sm-5">Окончание:</dt>
                    <dd class="col-sm-7">{{ $group->end_date ? $group->end_date->format('d.m.Y') : '-' }}</dd>
                    
                    <dt class="col-sm-5">Статус:</dt>
                    <dd class="col-sm-7">
                        @if($group->is_active)
                            <span class="badge bg-success">Активна</span>
                        @else
                            <span class="badge bg-danger">Неактивна</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-5">Студентов:</dt>
                    <dd class="col-sm-7"><span class="badge bg-info">{{ $group->students->count() }}</span></dd>
                </dl>
                
                @if($group->description)
                    <hr>
                    <p class="mb-0 small">{{ $group->description }}</p>
                @endif
            </div>
        </div>
        
        <!-- Teachers -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Преподаватели</h5>
            </div>
            <div class="card-body">
                @forelse($group->teachers as $teacher)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $teacher->full_name }}</strong><br>
                            <small class="text-muted">{{ $teacher->pivot->subject }}</small>
                        </div>
                        <a href="{{ route('admin.users.show', $teacher) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">Преподаватели не назначены</p>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Students List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Список студентов ({{ $group->students->count() }})</h5>
                @if($group->students->first() && $group->students->first()->synced_at)
                    <small class="text-muted">
                        Синхронизировано: {{ $group->students->first()->synced_at->format('d.m.Y H:i') }}
                    </small>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>№ студ. билета</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group->students->sortBy('last_name') as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->email ?? '-' }}</td>
                                    <td>{{ $student->phone ?? '-' }}</td>
                                    <td>{{ $student->student_card_number ?? '-' }}</td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success">Активен</span>
                                        @else
                                            <span class="badge bg-danger">Неактивен</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Список студентов пуст. Выполните синхронизацию.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Schedules -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Ближайшие занятия</h5>
            </div>
            <div class="card-body">
                @if($upcomingSchedules->isEmpty())
                    <p class="text-muted mb-0">Нет запланированных занятий</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Время</th>
                                    <th>Предмет</th>
                                    <th>Преподаватель</th>
                                    <th>Аудитория</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingSchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->date->format('d.m.Y') }}</td>
                                        <td>{{ $schedule->time_range }}</td>
                                        <td>{{ $schedule->subject }}</td>
                                        <td>{{ $schedule->teacher->short_name }}</td>
                                        <td>{{ $schedule->room ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection