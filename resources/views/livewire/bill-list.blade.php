<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start gap-2 sm:gap-4">
        <div class="w-full sm:w-auto">
            <x-inputs.select
                label="Showing"
                name="filterShowing"
                required
                wire:model.live="filterShowing"
                :options="[
                    '' => 'All Months',
                    'current' => 'Current Month Only',
                ]"
                without-empty
            />
        </div>
        <div class="w-full sm:w-auto">
            <x-inputs.select
                label="By Category"
                name="filterCategory"
                required
                wire:model.live="filterCategory"
                :options="$categoryOptions"
            />
        </div>
        <div class="flex items-end justify-end w-full sm:w-auto">
            <a
                class="flex items-center gap-1 text-secondary text-xs bg-zinc-100 border border-zinc-200 rounded-lg px-2 py-1 hover:bg-zinc-200 hover:border-zinc-300 transition duration-300 ease-in-out font-semibold"
                href="#"
                wire:click.prevent="resetFilters"
            >
                <x-heroicon-c-arrow-uturn-left class="w-4 h-4" />
                Reset Filters
            </a>
        </div>
    </div>

    @if ($bills->isNotEmpty())
        <div class="flex flex-col gap-6">
            @foreach ($bills as $bill)
                <div>
                    <x-bill-list-item :bill="$bill" />

                    <hr class="my-2">
                </div>
            @endforeach

            <div
                class="font-bold text-xl px-3 pb-4 flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-6">
                <h3>Bills Total:</h3>
                <div>{{ $billsTotal }}</div>
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
