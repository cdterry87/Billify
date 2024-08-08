<div class="flex flex-col gap-4">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 py-3 px-4 md:px-8">
        <a href="{{ route('home') }}">
            <h1 class="font-bold text-4xl logo text-primary hover:brightness-90 transition duration-200 ease-in-out">
                {{ config('app.name') }}
            </h1>
        </a>
        <div class="flex items-center gap-4 text-sm sm:text-base">
            <a
                href="#"
                alt="My Profile"
                title="My Profile"
                class="flex items-center gap-1 font-bold hover:brightness-150 transition duration-200 ease-in-out"
                @click.prevent="$wire.dispatch('openModal', {
                    title: 'My Profile',
                    component: 'my-profile-form',
                })"
            >
                <x-heroicon-s-user-circle class="h-8 w-8" />
                <span class="hidden sm:block">My Profile</span>
            </a>
            <form
                method="POST"
                action="{{ route('logout') }}"
                x-data
            >
                @csrf
                <a
                    href="{{ route('logout') }}"
                    alt="Logout"
                    title="Logout"
                    class="flex items-center gap-1 font-bold text-red-800 hover:brightness-150 transition duration-200 ease-in-out"
                    @click.prevent="$root.submit();"
                >
                    <x-heroicon-s-arrow-left-end-on-rectangle class="h-8 w-8" />
                    <span class="hidden sm:block">Logout</span>
                </a>
            </form>
        </div>
    </div>

    <div class="flex flex-col gap-6 mx-4 md:mx-8">
        @if (session()->has('alert-message'))
            <x-alert
                :type="session('alert-type')"
                :message="session('alert-message')"
            />
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <div class="flex flex-col gap-2 bg-white p-4 rounded-lg border border-zinc-200">
                <h3 class="font-bold text-lg">Annual Income</h3>
                <div class="text-sm">{{ auth()->user()->getYearlyIncome() }}</div>
            </div>
            <div class="flex flex-col gap-2 bg-white p-4 rounded-lg border border-zinc-200">
                <h3 class="font-bold text-lg">Monthly Income</h3>
                <div class="text-sm">{{ auth()->user()->getMonthlyIncome() }}</div>
            </div>
            <div class="flex flex-col gap-2 bg-white p-4 rounded-lg border border-zinc-200">
                <h3 class="font-bold text-lg">Bi-weekly Income</h3>
                <div class="text-sm">{{ auth()->user()->getBiWeeklyIncome() }}</div>
            </div>
            <div class="flex flex-col gap-2 bg-white p-4 rounded-lg border border-zinc-200">
                <h3 class="font-bold text-lg">Weekly Income</h3>
                <div class="text-sm">{{ auth()->user()->getWeeklyIncome() }}</div>
            </div>
        </div>

        <x-card title="My Bills">
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
