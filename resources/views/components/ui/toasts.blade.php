{{-- Trigger from Livewire with $this->dispatch('toast', type: 'success', message: '...'), or flash session('toast'). --}}
<div class="toasts" role="status" aria-live="polite" x-data="toasts(@js(session('toast')))" x-on:toast.window="push($event.detail)">
    <template x-for="toast in items" x-bind:key="toast.id">
        <div class="toast" x-bind:class="`toast--${toast.type}`">
            <span class="toast__message" x-text="toast.message"></span>
            <x-ui.button variant="ghost" size="sm" icon="x" label="Dismiss" x-on:click="dismiss(toast.id)" />
        </div>
    </template>
</div>
