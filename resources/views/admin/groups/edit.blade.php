@extends('layouts.app')

@section('title', 'Редактирование группы')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Редактирование группы: {{ $group->code }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.groups.update', $group) }}">
            @csrf
            @method('PUT')
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Основная информация</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Код группы *</label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code', $group->code) }}" 
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Курс *</label>
                            <select class="form-select @error('course') is-invalid @enderror" 
                                    id="course" 
                                    name="course" 
                                    required>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('course', $group->course) == $i ? 'selected' : '' }}>
                                        {{ $i }} курс
                                    </option>
                                @endfor
                            </select>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Название группы *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $group->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="speciality" class="form-label">Специальность</label>
                        <input type="text" 
                               class="form-control @error('speciality') is-invalid @enderror" 
                               id="speciality" 
                               name="speciality" 
                               value="{{ old('speciality', $group->speciality) }}">
                        @error('speciality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Дата начала обучения</label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $group->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Дата окончания обучения</label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $group->end_date?->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $group->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $group->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Группа активна
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Преподаватели и предметы</h5>
                </div>
                <div class="card-body">
                    <div id="teachers-container">
                        @forelse($group->teachers as $index => $teacher)
                            <div class="teacher-row mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select" name="teachers[{{ $index }}][id]">
                                            <option value="">Выберите преподавателя</option>
                                            @foreach($teachers as $t)
                                                <option value="{{ $t->id }}" 
                                                        {{ $teacher->id == $t->id ? 'selected' : '' }}>
                                                    {{ $t->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" 
                                               class="form-control" 
                                               name="teachers[{{ $index }}][subject]" 
                                               placeholder="Предмет"
                                               value="{{ $teacher->pivot->subject }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-teacher">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="teacher-row mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select" name="teachers[0][id]">
                                            <option value="">Выберите преподавателя</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">
                                                    {{ $teacher->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" 
                                               class="form-control" 
                                               name="teachers[0][subject]" 
                                               placeholder="Предмет">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-teacher" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    
                    <button type="button" class="btn btn-success btn-sm" id="add-teacher">
                        <i class="bi bi-plus"></i> Добавить преподавателя
                    </button>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary me-2">
                    Отмена
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Сохранить изменения
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Информация о группе</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>Создана:</dt>
                    <dd>{{ $group->created_at->format('d.m.Y H:i') }}</dd>
                    
                    <dt>Последнее обновление:</dt>
                    <dd>{{ $group->updated_at->format('d.m.Y H:i') }}</dd>
                    
                    <dt>Студентов в группе:</dt>
                    <dd>{{ $group->students->count() }}</dd>
                    
                    <dt>Расписаний:</dt>
                    <dd>{{ $group->schedules->count() }}</dd>
                </dl>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Действия</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.groups.sync-students', $group) }}">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-arrow-repeat"></i> Синхронизировать студентов
                    </button>
                </form>
                
                <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-info w-100">
                    <i class="bi bi-eye"></i> Просмотр группы
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let teacherIndex = {{ $group->teachers->count() ?: 1 }};
    const teachersContainer = document.getElementById('teachers-container');
    const teacherOptions = document.querySelector('select[name*="[id]"]').innerHTML;
    
    // Add teacher row
    document.getElementById('add-teacher').addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'teacher-row mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <select class="form-select" name="teachers[${teacherIndex}][id]">
                        ${teacherOptions}
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" 
                           class="form-control" 
                           name="teachers[${teacherIndex}][subject]" 
                           placeholder="Предмет">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-teacher">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        teachersContainer.appendChild(newRow);
        teacherIndex++;
        
        // Enable remove button on first row if there's more than one row
        if (teachersContainer.querySelectorAll('.teacher-row').length > 1) {
            teachersContainer.querySelector('.remove-teacher').disabled = false;
        }
    });
    
    // Remove teacher row
    teachersContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-teacher')) {
            const rows = teachersContainer.querySelectorAll('.teacher-row');
            if (rows.length > 1) {
                e.target.closest('.teacher-row').remove();
                
                // Disable remove button on last remaining row
                const remainingRows = teachersContainer.querySelectorAll('.teacher-row');
                if (remainingRows.length === 1) {
                    remainingRows[0].querySelector('.remove-teacher').disabled = true;
                }
            }
        }
    });
});
</script>
@endpush