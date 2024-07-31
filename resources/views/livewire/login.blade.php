<div class="h-screen text-white">
    <x-guest-background-video />

    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4">
        <div class="flex flex-col items-center gap-6 p-8 bg-zinc-100 text-zinc-800 w-full sm:w-120 rounded-lg">
            <x-logo-primary />

            <form
                wire:submit.prevent="login"
                class="flex flex-col gap-4 w-full"
            >
                <div class="flex flex-col gap-2">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        wire:model="email"
                        class="p-2 border border-zinc-300 rounded-lg"
                        required
                    >
                </div>
                <div class="flex flex-col gap-2">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        wire:model="password"
                        class="p-2 border border-zinc-300 rounded-lg"
                        required
                    >
                </div>
                <div class="mt-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-zinc-800 text-zinc-50 rounded-lg border border-zinc-900 w-full"
                    >Login</button>
                </div>
            </form>
            <div class="flex flex-row gap-2 text-xs sm:text-sm">
                <a
                    href="{{ route('register') }}"
                    class="text-secondary font-bold"
                >Create an Account</a>
                <span>|</span>
                <a
                    href="{{ route('forgot-password') }}"
                    class="text-secondary font-bold"
                >Forgot Password?</a>
            </div>
        </div>

        <x-guest-footer />
    </div>
</div>
