@extends('layouts.app')

@section('title', 'Создание группы')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Создание группы</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.groups.store') }}">
            @csrf
            
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
                                   value="{{ old('code') }}" 
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Уникальный код группы (например: ИТ-21)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Курс *</label>
                            <select class="form-select @error('course') is-invalid @enderror" 
                                    id="course" 
                                    name="course" 
                                    required>
                                <option value="">Выберите курс</option>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('course') == $i ? 'selected' : '' }}>
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
                               value="{{ old('name') }}" 
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
                               value="{{ old('speciality') }}">
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
                                   value="{{ old('start_date') }}">
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
                                   value="{{ old('end_date') }}">
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
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Преподаватели и предметы</h5>
                </div>
                <div class="card-body">
                    <div id="teachers-container">
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
                    <i class="bi bi-check-circle"></i> Создать группу
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Справка</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Код группы</strong> - уникальный идентификатор группы, используется в расписании и отчетах.</p>
                <p class="mb-2"><strong>Курс</strong> - текущий курс обучения группы (от 1 до 6).</p>
                <p class="mb-2"><strong>Преподаватели</strong> - можно назначить несколько преподавателей с разными предметами.</p>
                <hr>
                <p class="mb-0"><small>После создания группы можно будет синхронизировать список студентов из внешней системы.</small></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let teacherIndex = 1;
    const teachersContainer = document.getElementById('teachers-container');
    const teacherOptions = document.querySelector('select[name="teachers[0][id]"]').innerHTML;
    
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
    });
    
    // Remove teacher row
    teachersContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-teacher')) {
            e.target.closest('.teacher-row').remove();
        }
    });
});
</script>
@endpush