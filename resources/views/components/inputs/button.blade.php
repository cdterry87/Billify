@props(['label', 'type' => 'submit'])

<button
    {{ $attributes->merge([
        'alt' => $label,
        'aria-label' => $label,
        'class' => 'px-4 py-2 bg-zinc-800 text-zinc-50 rounded-lg border border-zinc-950 w-full',
        'title' => $label,
        'type' => $type,
    ]) }}
>
    {{ $label ?? $slot }}
</button>
