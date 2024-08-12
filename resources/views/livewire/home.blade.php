<div class="flex flex-col gap-4">
    <x-header />

    <div class="flex flex-col gap-6 mx-4 md:mx-8">
        @if (session()->has('alert-message'))
            <x-alert
                :type="session('alert-type')"
                :message="session('alert-message')"
                class="message--alert"
            />
        @elseif($notifications && count($notifications) > 0)
            <x-alert
                type="warning"
                class="notification--alert"
            >
                <p class="font-bold text-lg">You have bills that will be due soon!</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($notifications as $notification)
                        <li>{!! $notification !!}</li>
                    @endforeach
                </ul>
            </x-alert>
        @else
            <x-alert
                type="info"
                class="welcome--alert"
            >
                Welcome to <strong>Billify</strong>, <strong>{{ ucwords(auth()->user()->name) }}</strong>!
                View your income summary and get started tracking your monthly bills below!
            </x-alert>
        @endif

        <div class="income-summary--container grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
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
            <x-income-summary
                label="Monthly Remainder"
                value="{{ $monthlyRemainder }}"
            />
            <x-income-summary
                label="Debt/Income Ratio"
                value="{{ $dtiRatio }}"
            />
        </div>

        <div class="charts--container grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-lg border border-zinc-200 h-64">
                <livewire:livewire-column-chart
                    :key="$incomeVsBillsChart->reactiveKey()"
                    :column-chart-model="$incomeVsBillsChart"
                />
            </div>
            <div class="bg-white p-4 rounded-lg border border-zinc-200 h-64">
                <livewire:livewire-column-chart
                    :key="$topCategoriesChart->reactiveKey()"
                    :column-chart-model="$topCategoriesChart"
                />
            </div>
        </div>

        <x-card
            title="My Monthly Bills"
            class="bills--card"
        >
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

            <livewire:bill-list />
        </x-card>
    </div>

    <livewire:modal />
</div>
