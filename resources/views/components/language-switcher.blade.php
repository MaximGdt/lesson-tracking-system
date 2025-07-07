<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-globe"></i>
        @switch(app()->getLocale())
            @case('uk')
                UA
                @break
            @case('en')
                EN
                @break
            @case('ru')
                RU
                @break
            @default
                {{ strtoupper(app()->getLocale()) }}
        @endswitch
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item {{ app()->getLocale() == 'uk' ? 'active' : '' }}"
               href="{{ route('locale.set', ['locale' => 'uk']) }}">
                🇺🇦 Українська
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
               href="{{ route('locale.set', ['locale' => 'en']) }}">
                en English
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ app()->getLocale() == 'ru' ? 'active' : '' }}"
               href="{{ route('locale.set', ['locale' => 'ru']) }}">
                🇷🇺 Русский
            </a>
        </li>
    </ul>
</div>
