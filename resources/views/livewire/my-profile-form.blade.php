<div
    x-data="{
        activeTab: 'updateProfile',
    }"
    class="flex flex-col gap-4"
>
    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-2 sm:gap-4 font-semibold">
        <div class="text-zinc-600">
            <h4
                class="font-semibold text-xl"
                x-show="activeTab === 'updateProfile'"
            >Update your profile</h4>
            <h4
                class="font-semibold text-xl"
                x-show="activeTab === 'changePassword'"
            >Change your password</h4>
        </div>
        <div class="flex items-center gap-2 text-cyan-700 text-sm">
            <a
                href="#"
                @click="activeTab = 'updateProfile'"
                wire:click="$dispatch('resetAlerts')"
                x-show="activeTab !== 'updateProfile'"
                class="hover:text-cyan-900 transition duration-200 ease-in-out"
            >Update Profile</a>
            <a
                href="#"
                @click.prevent="activeTab = 'changePassword'"
                wire:click="$dispatch('resetAlerts')"
                x-show="activeTab !== 'changePassword'"
                class="hover:text-cyan-900 transition duration-200 ease-in-out"
            >Change Password</a>
        </div>
    </div>

    <div
        x-show="activeTab === 'updateProfile'"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <form
            wire:submit.prevent="updateProfile"
            class="flex flex-col gap-4 w-full"
        >
            <x-inputs.text
                label="Name"
                name="name"
                required
                wire:model="name"
            />
            <x-inputs.text
                label="Email"
                name="email"
                required
                type="email"
                wire:model="email"
            />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-inputs.text
                    label="Income"
                    name="income"
                    required
                    type="number"
                    wire:model="income"
                />
                <x-inputs.select
                    label="Frequency"
                    name="frequency"
                    required
                    wire:model="frequency"
                    :options="$frequencyOptions"
                />
            </div>
            <div class="mt-2">
                <x-actions.button label="Update Profile" />
            </div>
        </form>
    </div>

    <div
        x-show="activeTab === 'changePassword'"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <form
            wire:submit.prevent="changePassword"
            class="flex flex-col gap-4 w-full"
        >
            <x-inputs.text
                label="New Password"
                name="password"
                required
                type="password"
                wire:model="password"
            />
            <x-inputs.text
                label="Confirm Password"
                name="password_confirmation"
                required
                type="password"
                wire:model="password_confirmation"
            />

            <div class="mt-2">
                <x-actions.button label="Change Password" />
            </div>
        </form>
    </div>
</div>
