@props(['label', 'for' => null])

<label
    {{ $attributes->merge([
        'class' => 'text-sm font-bold text-zinc-600',
        'aria-label' => $label,
        'for' => $for,
    ]) }}
>
    {{ $label }}
</label>
