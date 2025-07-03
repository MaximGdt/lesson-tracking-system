@extends('layouts.app')

@section('title', 'Отчет по проведенным занятиям')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Отчет по проведенным занятиям</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.reports.export', 'excel') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('admin.reports.export', 'pdf') }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Печать
        </button>
    </div>
</div>

<!-- Report Parameters -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Параметры отчета:</h6>
        <div class="row">
            <div class="col-md-3">
                <strong>Период:</strong><br>
                {{ $filters['date_from'] ? \Carbon\Carbon::parse($filters['date_from'])->format('d.m.Y') : 'Начало' }} - 
                {{ $filters['date_to'] ? \Carbon\Carbon::parse($filters['date_to'])->format('d.m.Y') : 'Сегодня' }}
            </div>
            <div class="col-md-3">
                <strong>Преподаватель:</strong><br>
                {{ $filters['teacher_id'] ? \App\Models\User::find($filters['teacher_id'])->full_name : 'Все' }}
            </div>
            <div class="col-md-3">
                <strong>Группа:</strong><br>
                {{ $filters['group_id'] ? \App\Models\Group::find($filters['group_id'])->code : 'Все' }}
            </div>
            <div class="col-md-3">
                <strong>Сформирован:</strong><br>
                {{ now()->format('d.m.Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $summary['total_lessons'] }}</h3>
                <p class="mb-0">Всего занятий</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $summary['total_hours'] }}</h3>
                <p class="mb-0">Всего часов</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ count($summary['by_teacher']) }}</h3>
                <p class="mb-0">Преподавателей</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ count($summary['by_group']) }}</h3>
                <p class="mb-0">Групп</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">По типам занятий</h6>
            </div>
            <div class="card-body">
                <canvas id="typeChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">По месяцам</h6>
            </div>
            <div class="card-body">
                <canvas id="monthChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Table -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Детальная информация</h6>
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
                        <th>Предмет</th>
                        <th>Тип</th>
                        <th>Присутствовало</th>
                        <th>Отмечено</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lessons as $lesson)
                        <tr>
                            <td>{{ $lesson->schedule->date->format('d.m.Y') }}</td>
                            <td>{{ $lesson->schedule->time_range }}</td>
                            <td>{{ $lesson->schedule->group->code }}</td>
                            <td>{{ $lesson->schedule->teacher->short_name }}</td>
                            <td>{{ $lesson->schedule->subject }}</td>
                            <td><span class="badge bg-info">{{ $lesson->schedule->type_display }}</span></td>
                            <td>{{ $lesson->students_present ?? '-' }}</td>
                            <td>
                                <small>{{ $lesson->marked_at->format('d.m.Y H:i') }}<br>
                                {{ $lesson->markedBy->short_name }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Type Chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($summary['by_type'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($summary['by_type'])) !!},
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Month Chart
const monthCtx = document.getElementById('monthChart').getContext('2d');
new Chart(monthCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($summary['by_month'])) !!},
        datasets: [{
            label: 'Количество занятий',
            data: {!! json_encode(array_values($summary['by_month'])) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)'
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
</script>
@endpush