<div>
    @if ($bills->isNotEmpty())
        @foreach ($bills as $bill)
            <div
                class="flex flex-col sm:flex-row sm:items-end gap-2 justify-between sm:gap-6 hover:bg-slate-100 transition duration-300 ease-in-out p-3 rounded-lg">
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
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <button
                        class="bg-cyan-800 text-cyan-50 px-3 py-1 sm:p-1 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                        title="Edit Bill"
                        alt="Edit Bill"
                    >
                        <x-heroicon-c-pencil-square class="h-6 w-6" />
                        <span class="inline-block sm:hidden">Edit</span>
                    </button>
                    <button
                        class="bg-red-800 text-red-50 px-3 py-1 sm:p-1 rounded-lg flex items-center gap-2 hover:brightness-125 transition duration-300 ease-in-out"
                        title="Delete Bill"
                        alt="Delete Bill"
                    >
                        <x-heroicon-c-trash class="h-6 w-6" />
                        <span class="inline-block sm:hidden">Delete</span>
                    </button>
                </div>
            </div>

            <hr class="my-2">
        @endforeach
    @else
        <p>You have not added any bills yet.</p>
    @endif
</div>
