@extends('layouts.app')

@section('title', 'Календарь расписания')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Календарь расписания</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.schedule') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> Список
            </a>
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Печать
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Legend -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6>Легенда:</h6>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary" style="width: 20px; height: 20px; margin-right: 8px;"></div>
                        <span>Запланировано</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-success" style="width: 20px; height: 20px; margin-right: 8px;"></div>
                        <span>Проведено</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary" style="width: 20px; height: 20px; margin-right: 8px;"></div>
                        <span>Отменено</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/locales/uk.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: '{{ app()->getLocale() }}',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        height: 'auto',
        events: function(info, successCallback, failureCallback) {
            fetch(`{{ route('teacher.schedule.calendar') }}?start=${info.startStr}&end=${info.endStr}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => successCallback(data))
            .catch(error => {
                console.error('Error loading events:', error);
                failureCallback(error);
            });
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault();
            }
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', 
                `${info.event.extendedProps.group}\n` +
                `${info.event.extendedProps.room ? 'Аудитория: ' + info.event.extendedProps.room : ''}\n` +
                `Тип: ${info.event.extendedProps.type}`
            );
        }
    });
    
    calendar.render();
});
</script>
@endpush