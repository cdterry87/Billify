@props(['label', 'for' => null, 'required' => false])

<label
    {{ $attributes->merge([
        'class' => 'text-sm font-bold text-zinc-600',
        'aria-label' => $label,
        'for' => $for,
    ]) }}
>
    {{ $label }}
    @if ($required)
        <span class="text-red-700">*</span>
    @endif
</label>
