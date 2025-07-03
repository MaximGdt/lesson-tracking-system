@extends('layouts.app')

@section('title', 'Создание занятия')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Создание занятия</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.schedules.store') }}" id="scheduleForm">
            @csrf
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Основная информация</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_id" class="form-label">Группа *</label>
                            <select class="form-select @error('group_id') is-invalid @enderror" 
                                    id="group_id" 
                                    name="group_id" 
                                    required>
                                <option value="">Выберите группу</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" 
                                            {{ old('group_id') == $group->id ? 'selected' : '' }}
                                            data-teachers='@json($group->teachers->pluck('id'))'>
                                        {{ $group->code }} - {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Преподаватель *</label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                    id="teacher_id" 
                                    name="teacher_id" 
                                    required>
                                <option value="">Сначала выберите группу</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                            {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}
                                            class="teacher-option d-none">
                                        {{ $teacher->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="subject" class="form-label">Предмет *</label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}" 
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Тип занятия *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="lecture" {{ old('type') == 'lecture' ? 'selected' : '' }}>Лекция</option>
                                <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Практика</option>
                                <option value="lab" {{ old('type') == 'lab' ? 'selected' : '' }}>Лабораторная</option>
                                <option value="exam" {{ old('type') == 'exam' ? 'selected' : '' }}>Экзамен</option>
                                <option value="consultation" {{ old('type') == 'consultation' ? 'selected' : '' }}>Консультация</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Дата и время</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Дата *</label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', now()->format('Y-m-d')) }}" 
                                   min="{{ now()->format('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="start_time" class="form-label">Время начала *</label>
                            <input type="time" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time', '09:00') }}" 
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="end_time" class="form-label">Время окончания *</label>
                            <input type="time" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="{{ old('end_time', '10:30') }}" 
                                   required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="room" class="form-label">Аудитория</label>
                            <input type="text" 
                                   class="form-control @error('room') is-invalid @enderror" 
                                   id="room" 
                                   name="room" 
                                   value="{{ old('room') }}">
                            @error('room')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div id="holidayAlert" class="alert alert-warning d-none">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <span id="holidayMessage"></span>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Дополнительно</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Примечания</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary me-2">
                    Отмена
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Создать занятие
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Ближайшие праздники</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @forelse($holidays->take(5) as $holiday)
                        <li class="mb-2">
                            <strong>{{ $holiday->date->format('d.m.Y') }}</strong><br>
                            <small>{{ $holiday->name }}</small>
                        </li>
                    @empty
                        <li class="text-muted">Нет ближайших праздников</li>
                    @endforelse
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Справка</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><small><strong>Группа и преподаватель</strong> - выберите группу, затем преподавателя из списка назначенных на эту группу.</small></p>
                <p class="mb-2"><small><strong>Время занятия</strong> - стандартная пара длится 1 час 30 минут.</small></p>
                <p class="mb-0"><small><strong>Праздничные дни</strong> - система предупредит, если вы пытаетесь назначить занятие на выходной день.</small></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const groupSelect = document.getElementById('group_id');
    const teacherSelect = document.getElementById('teacher_id');
    const dateInput = document.getElementById('date');
    const holidayAlert = document.getElementById('holidayAlert');
    const holidayMessage = document.getElementById('holidayMessage');
    const holidays = @json($holidays->pluck('date', 'name'));
    
    // Handle group selection
    groupSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const teacherIds = selectedOption.dataset.teachers ? JSON.parse(selectedOption.dataset.teachers) : [];
        
        // Hide all teacher options
        document.querySelectorAll('.teacher-option').forEach(option => {
            option.classList.add('d-none');
            option.disabled = true;
        });
        
        // Show only teachers assigned to selected group
        if (teacherIds.length > 0) {
            teacherSelect.innerHTML = '<option value="">Выберите преподавателя</option>';
            teacherIds.forEach(id => {
                const option = document.querySelector(`.teacher-option[value="${id}"]`);
                if (option) {
                    option.classList.remove('d-none');
                    option.disabled = false;
                    teacherSelect.appendChild(option.cloneNode(true));
                }
            });
        } else {
            teacherSelect.innerHTML = '<option value="">Нет назначенных преподавателей</option>';
        }
    });
    
    // Check for holidays
    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        holidayAlert.classList.add('d-none');
        
        for (const [name, date] of Object.entries(holidays)) {
            if (date === selectedDate) {
                holidayMessage.textContent = `Внимание: ${selectedDate} - ${name}`;
                holidayAlert.classList.remove('d-none');
                break;
            }
        }
    });
    
    // Trigger initial group selection if old value exists
    if (groupSelect.value) {
        groupSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush