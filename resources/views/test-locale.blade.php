@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Тест локализации</h1>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5>Текущие настройки:</h5>
            <ul>
                <li>App Locale: <strong>{{ app()->getLocale() }}</strong></li>
                <li>Session Locale: <strong>{{ session('locale', 'not set') }}</strong></li>
                <li>Config Locale: <strong>{{ config('app.locale') }}</strong></li>
                <li>User Locale: <strong>{{ auth()->user()->locale ?? 'not logged in' }}</strong></li>
            </ul>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5>Переводы:</h5>
            <ul>
                <li>app.welcome: <strong>{{ __('app.welcome') }}</strong></li>
                <li>app.dashboard: <strong>{{ __('app.dashboard') }}</strong></li>
                <li>auth.login: <strong>{{ __('auth.login') }}</strong></li>
            </ul>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5>Переключить язык:</h5>
            <div class="btn-group">
                <a href="{{ route('locale.set', 'uk') }}" class="btn btn-outline-primary {{ app()->getLocale() == 'uk' ? 'active' : '' }}">
                    🇺🇦 Українська
                </a>
                <a href="{{ route('locale.set', 'en') }}" class="btn btn-outline-primary {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                    🇬🇧 English
                </a>
                <a href="{{ route('locale.set', 'ru') }}" class="btn btn-outline-primary {{ app()->getLocale() == 'ru' ? 'active' : '' }}">
                    🇷🇺 Русский
                </a>
            </div>
        </div>
    </div>
</div>
@endsections