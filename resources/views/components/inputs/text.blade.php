@props(['name', 'label', 'type' => 'text', 'id' => null, 'required' => false])

@php
    $id = $id ?? $name;
@endphp

<x-inputs.container
    :id="$id"
    :label="$label"
    :name="$name"
>
    <input
        {{ $attributes->merge([
            'aria-label' => $label,
            'aria-required' => $required,
            'class' => 'p-2 border border-zinc-300 rounded-lg',
            'id' => $id,
            'name' => $name,
            'required' => $required,
            'type' => $type,
        ]) }}
    >
</x-inputs.container>
