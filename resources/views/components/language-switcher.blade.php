<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-globe"></i> {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item {{ app()->getLocale() == 'uk' ? 'active' : '' }}" 
               href="{{ route('locale.set', 'uk') }}">
                🇺🇦 Українська
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
               href="{{ route('locale.set', 'en') }}">
                🇬🇧 English
            </a>
        </li>
    </ul>
</div>
