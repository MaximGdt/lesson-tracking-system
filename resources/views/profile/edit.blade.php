@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Мой профиль</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Profile Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Личная информация</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Фамилия</label>
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
                            <label for="first_name" class="form-label">Имя</label>
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
                            <label for="email" class="form-label">Email</label>
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
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Изменить пароль</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль</label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Новый пароль</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Подтвердите новый пароль</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Изменить пароль
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Account Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Информация об аккаунте</h5>
            </div>
            <div class="card-body">
                <p><strong>Роли:</strong></p>
                @foreach($user->roles as $role)
                    <span class="badge bg-primary mb-1">{{ $role->display_name }}</span>
                @endforeach
                
                <hr>
                
                <p class="mb-1"><strong>Зарегистрирован:</strong><br>
                {{ $user->created_at->format('d.m.Y H:i') }}</p>
                
                @if($user->last_login_at)
                    <p class="mb-1"><strong>Последний вход:</strong><br>
                    {{ $user->last_login_at->format('d.m.Y H:i') }}</p>
                @endif
                
                <p class="mb-0"><strong>Статус:</strong><br>
                @if($user->is_active)
                    <span class="badge bg-success">Активен</span>
                @else
                    <span class="badge bg-danger">Неактивен</span>
                @endif
                </p>
            </div>
        </div>
        
        @if($user->isTeacher())
            <!-- Teacher Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Моя статистика</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Групп:</strong> {{ $user->groups->count() }}
                    </p>
                    <p class="mb-2">
                        <strong>Занятий в этом месяце:</strong> 
                        {{ $user->schedules()->whereMonth('date', now()->month)->count() }}
                    </p>
                    <p class="mb-0">
                        <strong>Проведено в этом месяце:</strong> 
                        {{ $user->schedules()
                            ->whereMonth('date', now()->month)
                            ->whereHas('lesson', function($q) {
                                $q->where('is_conducted', true);
                            })->count() }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection