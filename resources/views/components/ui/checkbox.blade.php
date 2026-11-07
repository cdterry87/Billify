@props(['name', 'label', 'id' => null])

@php
    $id ??= str_replace('.', '-', $name);
@endphp

<label class="checkbox" for="{{ $id }}">
    <input {{ $attributes->class('checkbox__input')->merge(['type' => 'checkbox', 'id' => $id, 'name' => $name]) }}>
    {{ $label }}
</label>
