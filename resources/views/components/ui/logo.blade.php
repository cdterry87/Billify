<a {{ $attributes->class('logo')->merge(['href' => url('/')]) }}>
    <svg class="logo__mark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <rect x="3" y="13" width="5" height="8" rx="1.5" />
        <rect x="9.5" y="8" width="5" height="13" rx="1.5" />
        <rect x="16" y="3" width="5" height="18" rx="1.5" />
    </svg>
    {{ config('app.name') }}
</a>
