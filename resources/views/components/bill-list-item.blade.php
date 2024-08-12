@props(['bill'])

<div x-data="{ isDeleting: false }">
    <div
        x-show="!isDeleting"
        class="flex flex-col sm:flex-row sm:items-end gap-2 justify-between sm:gap-6 hover:bg-slate-100 transition duration-300 ease-in-out p-3 rounded-lg"
    >
        <div class="flex flex-col gap-2 flex-1">
            <h6 class="font-bold text-xl">{{ $bill->name }}</h6>
            <div class="flex flex-wrap items-center text-xs gap-x-3 gap-y-1 w-full">
                <p class="bg-emerald-100 text-emerald-700 py-1 px-2 rounded-lg">
                    Amount:
                    <span class="font-semibold">
                        {{ $bill->getAmountAsCurrency() }}
                    </span>
                </p>
                <p class="bg-cyan-100 text-cyan-700 py-1 px-2 rounded-lg">
                    Category:
                    <span class="font-semibold">
                        {{ $bill->getCategoryValue() }}
                    </span>
                </p>
                <p class="bg-rose-200 text-rose-700 py-1 px-2 rounded-lg">
                    Day Due:
                    <span class="font-semibold">
                        {{ $bill->day }}
                    </span>
                </p>
                @if ($bill->january)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        January
                    </p>
                @endif
                @if ($bill->february)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        February
                    </p>
                @endif
                @if ($bill->march)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        March
                    </p>
                @endif
                @if ($bill->april)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        April
                    </p>
                @endif
                @if ($bill->may)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        May
                    </p>
                @endif
                @if ($bill->june)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        June
                    </p>
                @endif
                @if ($bill->july)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        July
                    </p>
                @endif
                @if ($bill->august)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        August
                    </p>
                @endif
                @if ($bill->september)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        September
                    </p>
                @endif
                @if ($bill->october)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        October
                    </p>
                @endif
                @if ($bill->november)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        November
                    </p>
                @endif
                @if ($bill->december)
                    <p class="bg-violet-200 text-violet-700 py-1 px-2 rounded-lg font-semibold">
                        December
                    </p>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-center gap-3">
            <button
                class="bg-cyan-800 text-cyan-50 px-3 py-1 sm:p-1 xl:px-3 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                title="Edit Bill"
                alt="Edit Bill"
                @click.prevent="$wire.dispatch('openModal', {
                            title: 'Edit Bill',
                            component: 'bill-form',
                            params: {
                                modelId: {{ $bill->id }},
                            },
                        })"
            >
                <x-heroicon-c-pencil-square class="h-6 w-6" />
                <span class="inline-block sm:hidden xl:inline-block">Edit</span>
            </button>
            <button
                class="bg-red-800 text-red-50 px-3 py-1 sm:p-1 xl:px-3 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                title="Delete Bill"
                alt="Delete Bill"
                @click.prevent="isDeleting = true"
            >
                <x-heroicon-c-trash class="h-6 w-6" />
                <span class="inline-block sm:hidden xl:inline-block">Delete</span>
            </button>
        </div>
    </div>
    <div
        x-show="isDeleting"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="py-6 flex flex-col sm:flex-row sm:items-end gap-2 justify-between sm:gap-6 hover:bg-slate-100 transition duration-300 ease-in-out p-3 rounded-lg"
    >
        <div class="flex-1 font-semibold text-xl text-zinc-600">
            Are you sure you want to delete <strong class="text-red-700 underline">"{{ $bill->name }}"</strong>?
        </div>
        <div class="flex items-center justify-center gap-3">
            <button
                class="bg-red-800 text-red-50 px-3 py-1 sm:p-1 xl:px-3 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                title="Yes, Delete Bill"
                alt="Yes, Delete Bill"
                wire:click.prevent="deleteBill({{ $bill->id }})"
            >
                <x-heroicon-c-trash class="h-6 w-6" />
                <span class="inline-block sm:hidden xl:inline-block">Yes, Delete</span>
            </button>
            <button
                class="bg-cyan-800 text-cyan-50 px-3 py-1 sm:p-1 xl:px-3 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                title="No, Cancel"
                alt="No, Cancel"
                @click.prevent="isDeleting = false"
            >
                <x-heroicon-c-no-symbol class="h-6 w-6" />
                <span class="inline-block sm:hidden xl:inline-block">No, Cancel</span>
            </button>
        </div>
    </div>
</div>
