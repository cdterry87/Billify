<div class="bg-zinc-100 text-zinc-800 h-screen min-h-screen">
    <div class="flex flex-col gap-4 items-center justify-center h-full mx-4 py-6">
        <div class="flex flex-col gap-4 items-center justify-center w-full sm:w-120">
            <x-logo />
            <h3 class="text-zinc-500">
                Enter your email to reset your password.
            </h3>
            <div class="flex flex-col items-center gap-6 px-8 w-full">
                <form
                    wire:submit.prevent="submit"
                    class="flex flex-col gap-4 w-full"
                >
                    <x-inputs.text
                        label="Email"
                        name="email"
                        required
                        type="email"
                        wire:model="email"
                    />
                    <div class="mt-2">
                        <x-actions.button label="Send Password Reset Link" />
                    </div>
                </form>
                <div class="flex items-center gap-2 text-xs sm:text-sm">
                    <x-actions.link
                        class="text-secondary"
                        :href="route('login')"
                        label="Login"
                    />
                    <span>or</span>
                    <x-actions.link
                        class="text-secondary"
                        :href="route('register')"
                        label="Create an Account"
                    />
                </div>
            </div>
        </div>

        <x-footer />
    </div>
</div>
