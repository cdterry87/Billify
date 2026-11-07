{{-- Open with $dispatch('open-modal', { name: '...' }) and close with 'close-modal'. --}}
@props(['name', 'title'])

<dialog
    {{ $attributes->class('modal')->merge(['aria-labelledby' => "{$name}-title"]) }}
    wire:ignore.self
    x-data
    x-on:open-modal.window="$event.detail.name === @js($name) && $el.showModal()"
    x-on:close-modal.window="$event.detail.name === @js($name) && $el.close()"
    x-on:click="$event.target === $el && $el.close()"
>
    <div class="modal__panel">
        <header class="modal__header">
            <h2 class="modal__title" id="{{ $name }}-title">{{ $title }}</h2>
            <x-ui.button variant="ghost" size="sm" icon="x" label="Close" x-on:click="$el.closest('dialog').close()" />
        </header>
        {{ $slot }}
    </div>
</dialog>
