<div class="flex flex-col gap-4">
    <x-header />

    <div class="flex flex-col gap-6 mx-4 md:mx-8">
        @if (session()->has('alert-message'))
            <x-alert
                :type="session('alert-type')"
                :message="session('alert-message')"
            />
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <x-income-summary
                label="Annual Income"
                value="{{ $yearlyIncome }}"
            />
            <x-income-summary
                label="Monthly Income"
                value="{{ $monthlyIncome }}"
            />
            <x-income-summary
                label="Bi-Weekly Income"
                value="{{ $biWeeklyIncome }}"
            />
            <x-income-summary
                label="Weekly Income"
                value="{{ $weeklyIncome }}"
            />
        </div>

        <x-card title="My Monthly Bills">
            <x-slot name="primaryAction">
                <x-actions.button
                    label="Add Bill"
                    icon="heroicon-c-plus-circle"
                    @click.prevent="$wire.dispatch('openModal', {
                        title: 'Add Bill',
                        component: 'bill-form',
                    })"
                />
            </x-slot>

            <livewire:bill-list :monthly-income="$monthlyIncome" />
        </x-card>
    </div>

    <livewire:modal />
</div>
