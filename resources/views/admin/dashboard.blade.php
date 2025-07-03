@extends('layouts.app')

@section('title', 'Панель управления')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Панель управления</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Печать
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download"></i> Экспорт
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Пользователей
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['users_count'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Групп
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['groups_count'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-collection fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Занятий сегодня
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['lessons_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                            Проведено за месяц
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['lessons_month'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-bar-chart"></i> Статистика занятий за неделю
                </h6>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-pie-chart"></i> Занятия по типам
                </h6>
            </div>
            <div class="card-body">
                <canvas id="typesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-clock-history"></i> Последние отмеченные занятия
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Преподаватель</th>
                                <th>Группа</th>
                                <th>Предмет</th>
                                <th>Время</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->schedule->teacher->short_name }}</td>
                                    <td>{{ $lesson->schedule->group->code }}</td>
                                    <td>{{ $lesson->schedule->subject }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $lesson->marked_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Нет данных
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-calendar-event"></i> Ближайшие занятия
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Группа</th>
                                <th>Преподаватель</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->date->format('d.m') }}</td>
                                    <td>{{ $schedule->start_time->format('H:i') }}</td>
                                    <td>{{ $schedule->group->code }}</td>
                                    <td>{{ $schedule->teacher->short_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Нет запланированных занятий
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklyData['labels']) !!},
            datasets: [{
                label: 'Проведено занятий',
                data: {!! json_encode($weeklyData['values']) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Types Chart
    const typesCtx = document.getElementById('typesChart').getContext('2d');
    new Chart(typesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($typesData['labels']) !!},
            datasets: [{
                data: {!! json_encode($typesData['values']) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush