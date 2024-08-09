<div class="flex flex-col gap-6">
    <div class="flex flex-col-reverse lg:flex-row gap-2 items-end justify-between lg:gap-6">
        <div class="flex flex-col lg:flex-row items-start gap-2 lg:gap-4 w-full lg:w-auto">
            <div class="w-full lg:w-auto">
                <x-inputs.text
                    label="Search"
                    type="search"
                    name="filterSearch"
                    placeholder="Search for bills..."
                    wire:model.live.debounce.500ms="filterSearch"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                <div class="w-full lg:w-auto">
                    <x-inputs.select
                        label="Showing"
                        name="filterShowing"
                        wire:model.live="filterShowing"
                        :options="[
                            '' => 'All Months',
                            'current' => 'Current Month Only',
                        ]"
                        without-empty
                    />
                </div>
                <div class="w-full lg:w-auto">
                    <x-inputs.select
                        label="By Category"
                        name="filterCategory"
                        wire:model.live="filterCategory"
                        :options="$categoryOptions"
                    />
                </div>
            </div>
            <div class="flex items-end justify-end w-full lg:w-auto">
                <a
                    class="flex items-center gap-1 text-secondary text-xs bg-zinc-100 border border-zinc-200 rounded-lg px-2 py-1 hover:bg-zinc-200 hover:border-zinc-300 transition duration-300 ease-in-out font-semibold"
                    href="#"
                    wire:click.prevent="resetFilters"
                >
                    <x-heroicon-c-arrow-uturn-left class="w-4 h-4" />
                    Reset
                </a>
            </div>
        </div>
        <div class="font-bold text-xl italic text-zinc-600">
            {{ $billsCount ?? 0 }} Results
        </div>
    </div>

    @if ($bills->isNotEmpty())
        <div class="flex flex-col">
            @foreach ($bills as $bill)
                <div>
                    <x-bill-list-item :bill="$bill" />

                    <hr class="my-2">
                </div>
            @endforeach

            <div class="flex flex-col gap-2 font-bold text-base sm:text-xl px-3 pb-4">
                <div class="flex flex-row items-center justify-between gap-2 sm:gap-6">
                    <h3>Monthly Income:</h3>
                    <div class="text-emerald-700">{{ $monthlyIncome }}</div>
                </div>
                <div class="flex flex-row items-center justify-between gap-2 sm:gap-6">
                    <h3>Bills Total:</h3>
                    <div class="text-rose-700">- {{ $billsTotal }}</div>
                </div>
                <hr>
                <div>
                    <div class="flex flex-row items-center justify-between gap-2 sm:gap-6">
                        <h3>Monthly Remainder:</h3>
                        <div class="{{ $hasRemainder ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $monthlyRemainder }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div class="flex flex-col gap-2 items-center justify-center text-center py-12 px-6 tracking-wider">
            @if ($filterCategory || $filterShowing)
                <p class="text-lg sm:text-3xl text-zinc-700">No results were found.</p>
                <p class="text-xs sm:text-base text-zinc-600">Please try a different filter.</p>
            @else
                <p class="text-lg sm:text-3xl text-zinc-700">You have not added any bills yet.</p>
                <p class="text-xs sm:text-base text-zinc-600">Click the "Add Bill" button above to add a new bill.</p>
            @endif
        </div>
    @endif
</div>
