@props(['name', 'label', 'id' => null, 'options' => [], 'required' => false, 'withoutEmpty' => false])

@php
    $id = $id ?? $name;
@endphp

<x-inputs.container
    :id="$id"
    :label="$label"
    :name="$name"
    :required="$required"
>
    <select
        {{ $attributes->merge([
            'aria-label' => $label,
            'aria-required' => $required,
            'class' => 'p-2.5 border bg-white border-zinc-300 rounded-lg',
            'id' => $id,
            'name' => $name,
            'required' => $required,
        ]) }}
    >
        @if (!$withoutEmpty)
            <option value="">Select {{ $label }}</option>
        @endif
        @if ($options)
            @foreach ($options as $key => $option)
                <option value="{{ $key }}">{{ $option }}</option>
            @endforeach
        @endif
    </select>
</x-inputs.container>
