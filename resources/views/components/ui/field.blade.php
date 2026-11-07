@props(['name', 'label', 'as' => 'input', 'type' => 'text', 'hint' => null, 'required' => false, 'id' => null, 'options' => [], 'placeholder' => null])

@php
    $id ??= str_replace('.', '-', $name);
    $error = ($errors ?? null)?->first($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $error ? "{$id}-error" : null])->filter()->implode(' ');
    $control = $attributes->class('field__control')->merge([
        'id' => $id,
        'name' => $name,
        'aria-required' => $required ? 'true' : null,
        'aria-invalid' => $error ? 'true' : null,
        'aria-describedby' => $describedBy ?: null,
    ]);
@endphp

<div class="field">
    <label class="field__label" for="{{ $id }}">
        {{ $label }}
        @if ($required)<span class="field__required" aria-hidden="true">*</span>@endif
    </label>

    @if ($as === 'select')
        <select {{ $control }}>
            @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
            @foreach ($options as $value => $optionLabel)
                <option value="{{ $value }}">{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($as === 'textarea')
        <textarea {{ $control->merge(['rows' => 3, 'placeholder' => $placeholder]) }}>{{ $slot }}</textarea>
    @else
        <input {{ $control->merge(['type' => $type, 'placeholder' => $placeholder]) }}>
    @endif

    @if ($hint)<p class="field__hint" id="{{ $id }}-hint">{{ $hint }}</p>@endif
    @if ($error)<p class="field__error" id="{{ $id }}-error">{{ $error }}</p>@endif
</div>
