<div class="bg-zinc-100 text-zinc-800 h-screen min-h-screen">
    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4 py-6">
        <div class="flex flex-col gap-4 items-center justify-center w-full sm:w-120">
            <x-logo-primary />
            <h3 class="text-zinc-500">
                Reset your password.
            </h3>
            <div class="flex flex-col items-center gap-6 px-8 w-full">
                <form
                    wire:submit.prevent="resetPassword"
                    class="flex flex-col gap-4 w-full"
                >
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
                    <div class="mt-2">
                        <x-inputs.button label="Reset Password" />
                    </div>
                </form>
                <div class="text-xs sm:text-sm">
                    <x-inputs.link
                        class="text-secondary"
                        :href="route('login')"
                        label="Return to Login"
                    />
                </div>
            </div>
        </div>

        <x-guest-footer />
    </div>
</div>
