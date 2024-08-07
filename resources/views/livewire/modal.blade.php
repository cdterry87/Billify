<div
    x-data="{ isOpen: @entangle('isOpen') }"
    x-show="isOpen"
    class="fixed inset-0 flex items-center justify-center z-50"
>
    <div class="fixed inset-0 bg-black opacity-50"></div>

    <div
        @click.away="$wire.close()"
        class="bg-white rounded-lg border border-zinc-200 shadow-lg p-6 w-full sm:w-2/3 lg:w-1/2 z-10 mx-4"
    >
        <div class="flex flex-col gap-3">
            <div class="flex justify-between items-center pl-2">
                <h3 class="text-2xl font-bold">{{ $title }}</h3>
                <button
                    @click.prevent="$wire.close()"
                    class="text-gray-600 hover:text-gray-900"
                >
                    <x-heroicon-c-x-circle class="h-6 w-6" />
                </button>
            </div>

            @if (session()->has('modal-alert-message'))
                <x-alert
                    :type="session('modal-alert-type')"
                    :message="session('modal-alert-message')"
                />
            @endif

            <div class="max-h-96 overflow-y-auto pl-2 pr-4">
                @if ($component)
                    @livewire($component, $params, key($component))
                @endif
            </div>
        </div>
    </div>
</div>
