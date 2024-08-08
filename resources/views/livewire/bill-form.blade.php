<div class="flex flex-col gap-4">
    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-2 sm:gap-4 font-semibold">
        <h4 class="font-semibold text-xl text-zinc-600">Manage your bills</h4>
        <a
            href="#"
            wire:click="resetForm"
            class="text-cyan-700 hover:text-cyan-900 transition duration-200 ease-in-out flex items-center gap-1 font-semibold text-sm"
        >
            <x-heroicon-c-plus-circle class="h-6 w-6" />
            New Bill
        </a>
    </div>

    <form
        wire:submit.prevent="submit"
        class="flex flex-col gap-4 w-full"
    >
        <x-inputs.text
            label="Name"
            name="name"
            required
            wire:model="name"
        />
        <x-inputs.select
            label="Category"
            name="category"
            required
            wire:model="category"
            :options="$categoryOptions"
        />
        <x-inputs.textarea
            label="Description"
            name="description"
            wire:model="description"
        />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-inputs.text
                label="Amount"
                name="amount"
                type="number"
                required
                wire:model="amount"
            />
            <x-inputs.text
                label="Day of Month"
                name="day"
                type="number"
                required
                wire:model="day"
                min="1"
                max="31"
            />
        </div>

        <div class="flex flex-col gap-1">
            <h5 class="text-zinc-600 font-semibold text-lg">Monthly Recurrence (Optional)</h5>

            <p class="text-zinc-500 text-xs">
                <strong>Select only the months in which this bill recurs.</strong>
                If you don't select any months, the system will assume
                the bill recurs every month. If one or more months are selected below, the bill will
                <strong>ONLY</strong> be displayed during the month(s) selected.
                <strong>It is not necessary to check all months.</strong>
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-3">
            <x-inputs.checkbox
                label="January"
                name="january"
                wire:model="january"
            />
            <x-inputs.checkbox
                label="February"
                name="february"
                wire:model="february"
            />
            <x-inputs.checkbox
                label="March"
                name="march"
                wire:model="march"
            />
            <x-inputs.checkbox
                label="April"
                name="april"
                wire:model="april"
            />
            <x-inputs.checkbox
                label="May"
                name="may"
                wire:model="may"
            />
            <x-inputs.checkbox
                label="June"
                name="june"
                wire:model="june"
            />
            <x-inputs.checkbox
                label="July"
                name="july"
                wire:model="july"
            />
            <x-inputs.checkbox
                label="August"
                name="august"
                wire:model="august"
            />
            <x-inputs.checkbox
                label="September"
                name="september"
                wire:model="september"
            />
            <x-inputs.checkbox
                label="October"
                name="october"
                wire:model="october"
            />
            <x-inputs.checkbox
                label="November"
                name="november"
                wire:model="november"
            />
            <x-inputs.checkbox
                label="December"
                name="december"
                wire:model="december"
            />
        </div>

        <div class="mt-2 flex items-center gap-4">
            <x-actions.button label="Submit" />
            @if ($modelId)
                <x-actions.button
                    label="Delete"
                    custom-classes="bg-red-800 text-red-50 border-red-900 hover:bg-red-700 hover:border-red-800"
                    wire:click="delete"
                />
            @endif
        </div>
    </form>
</div>
