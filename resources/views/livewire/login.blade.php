<div class="h-screen text-white">
    <x-guest-background-video />

    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4">
        <div class="flex flex-col items-center gap-6 p-8 bg-zinc-100 text-zinc-800 w-full sm:w-120 rounded-lg">
            <x-logo />

            <form
                wire:submit.prevent="submit"
                class="flex flex-col gap-4 w-full"
            >
                @if (session()->has('alert-message'))
                    <x-alert
                        :type="session('alert-type')"
                        :message="session('alert-message')"
                    />
                @endif

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
                <div class="mt-2">
                    <x-actions.button
                        type="submit"
                        label="Login"
                    />
                </div>
            </form>
            <div class="flex flex-row gap-2 text-xs sm:text-sm">
                <x-actions.link
                    class="text-secondary"
                    :href="route('register')"
                    label="Create an Account"
                />
                <span>|</span>
                <x-actions.link
                    class="text-secondary"
                    :href="route('forgot-password')"
                    label="Forgot Password?"
                />
            </div>
        </div>

        <x-guest-footer />
    </div>
</div>
