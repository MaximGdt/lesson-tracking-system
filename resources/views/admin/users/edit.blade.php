@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Редактирование пользователя: {{ $user->full_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Личная информация</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Фамилия *</label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $user->last_name) }}" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">Имя *</label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $user->first_name) }}" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Отчество</label>
                            <input type="text" 
                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                   id="middle_name" 
                                   name="middle_name" 
                                   value="{{ old('middle_name', $user->middle_name) }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}" 
                                   placeholder="+380501234567">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Безопасность</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Оставьте поля паролей пустыми, если не хотите изменять пароль.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Новый пароль</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Минимум 8 символов</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Роли и доступ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Выберите роли *</label>
                        @error('roles')
                            <div class="text-danger mb-2">{{ $message }}</div>
                        @enderror
                        
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->id }}" 
                                       id="role_{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    <strong>{{ $role->display_name }}</strong>
                                    @if($role->description)
                                        <br>
                                        <small class="text-muted">{{ $role->description }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr>
                    
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Пользователь активен
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
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
                <h5 class="mb-0">Информация об аккаунте</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>ID пользователя:</dt>
                    <dd>{{ $user->id }}</dd>
                    
                    <dt>Зарегистрирован:</dt>
                    <dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                    
                    <dt>Последний вход:</dt>
                    <dd>
                        @if($user->last_login_at)
                            {{ $user->last_login_at->format('d.m.Y H:i') }}
                        @else
                            Не входил
                        @endif
                    </dd>
                    
                    <dt>Email подтвержден:</dt>
                    <dd>
                        @if($user->email_verified_at)
                            <span class="text-success">Да</span>
                        @else
                            <span class="text-danger">Нет</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
        
        @if($user->isTeacher())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Статистика преподавателя</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Групп:</dt>
                        <dd>{{ $user->groups->count() }}</dd>
                        
                        <dt>Запланированных занятий:</dt>
                        <dd>{{ $user->schedules()->where('date', '>=', today())->count() }}</dd>
                        
                        <dt>Проведено в этом месяце:</dt>
                        <dd>
                            {{ $user->schedules()
                                ->whereMonth('date', now()->month)
                                ->whereHas('lesson', function($q) {
                                    $q->where('is_conducted', true);
                                })->count() }}
                        </dd>
                    </dl>
                </div>
            </div>
        @endif
        
        @if($user->id === auth()->id())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                Вы редактируете свой собственный аккаунт. Будьте осторожны с изменением ролей и статуса.
            </div>
        @endif
    </div>
</div>
@endsection