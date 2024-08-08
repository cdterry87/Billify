@props(['id', 'label', 'name', 'required' => false])

<div class="flex flex-col gap-1">
    <x-inputs.label
        :for="$id"
        :label="$label"
        :required="$required"
    />
    {{ $slot }}
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</div>
