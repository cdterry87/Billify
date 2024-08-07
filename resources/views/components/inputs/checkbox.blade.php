@props(['name', 'label', 'type' => 'text', 'id' => null, 'required' => false])

@php
    $id = $id ?? $name;
@endphp

<div class="flex flex-col gap-1">
    <div class="flex items-center gap-2">
        <input
            {{ $attributes->merge([
                'aria-label' => $label,
                'aria-required' => $required,
                'class' => 'h-4 w-4 border border-zinc-300 rounded-lg',
                'id' => $id,
                'name' => $name,
                'type' => 'checkbox',
            ]) }}
        >

        <x-inputs.label
            :for="$id"
            :label="$label"
        />
    </div>
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</div>
