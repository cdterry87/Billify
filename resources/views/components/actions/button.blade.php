@props([
    'label',
    'type' => null,
    'icon' => null,
    'customClasses' => 'bg-zinc-800 text-zinc-50 border-zinc-950 hover:bg-zinc-700 hover:border-zinc-800',
])

<button
    {{ $attributes->merge([
        'alt' => $label,
        'aria-label' => $label,
        'class' =>
            'flex items-center justify-center gap-2 px-4 py-2 rounded-lg border w-full transition duration-300 ease-in-out disabled:bg-zinc-300 disabled:border-zinc-300 disabled:text-zinc-800 ' .
            $customClasses,
        'title' => $label,
        'type' => $type,
        'wire:loading.attr' => 'disabled',
    ]) }}
>
    @if ($icon)
        <x-dynamic-component
            :component="$icon"
            class="h-6 w-6"
        />
    @endif
    {{ $label ?? $slot }}
</button>
