<div class="bg-zinc-100 text-zinc-800 min-h-screen">
    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4 py-6">
        <div class="flex flex-col gap-4 items-center justify-center w-full sm:w-120">
            <x-logo />
            <h3 class="text-zinc-500">
                Create an account. Manage your bills. <em class="italic">Simple</em>.
            </h3>
            <div class="flex flex-col items-center gap-6 px-8 w-full">
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
                    <x-inputs.text
                        label="Email"
                        name="email"
                        required
                        type="email"
                        wire:model="email"
                    />
                    <x-inputs.text
                        label="Password"
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
                        <x-actions.button label="Register" />
                    </div>
                </form>
                <div class="text-xs sm:text-sm">
                    <x-actions.link
                        class="text-secondary"
                        :href="route('login')"
                        label="Already have an account? Login"
                    />
                </div>
            </div>
        </div>

        <x-guest-footer />
    </div>
</div>
