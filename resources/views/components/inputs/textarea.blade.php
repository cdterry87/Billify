@props(['name', 'label', 'id' => null, 'required' => false, 'rows' => 3])

@php
    $id = $id ?? $name;
@endphp

<x-inputs.container
    :id="$id"
    :label="$label"
    :name="$name"
>
    <textarea
        {{ $attributes->merge([
            'aria-label' => $label,
            'aria-required' => $required,
            'class' => 'p-2 border border-zinc-300 rounded-lg',
            'id' => $id,
            'name' => $name,
            'required' => $required,
            'rows' => $rows,
        ]) }}
    >
        {{ $slot }}
    </textarea>
</x-inputs.container>
