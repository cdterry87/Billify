@props([
    'href',
    'label',
    'icon' => null,
    'target' => '_self',
    'rel' => 'noopener',
    'referrerpolicy' => 'no-referrer',
])

<a
    {{ $attributes->merge([
        'aria-label' => $label,
        'class' =>
            'font-bold hover:brightness-75 transition duration-300 ease-in-out flex items-center justify-center gap-2',
        'href' => $href,
        'referrerpolicy' => $referrerpolicy,
        'rel' => $rel,
        'target' => $target,
        'title' => $label,
    ]) }}>
    @if ($icon)
        <x-dynamic-component
            :component="$icon"
            class="h-6 w-6"
        />
    @endif
    {{ $label }}
</a>
