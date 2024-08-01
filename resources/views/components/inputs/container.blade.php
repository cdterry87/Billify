@props(['id', 'label', 'name'])

<div class="flex flex-col gap-1">
    <x-inputs.label
        :for="$id"
        :label="$label"
    />
    {{ $slot }}
    @error($name)
        <x-inputs.error :message="$message" />
    @enderror
</div>
