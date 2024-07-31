<div class="bg-zinc-100 text-zinc-800">
    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4 py-6">
        <div class="flex flex-col gap-4 items-center justify-center w-full sm:w-120">
            <x-logo-primary />
            <h3>
                Create an account. Manage your bills. <span class="italic">Simple</span>.
            </h3>
            <div class="flex flex-col items-center gap-6 px-8 w-full">
                <form
                    wire:submit.prevent="login"
                    class="flex flex-col gap-4 w-full"
                >
                    <div class="flex flex-col gap-2">
                        <label for="name">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            wire:model="name"
                            class="p-2 border border-zinc-300 rounded-lg"
                            required
                        >
                    </div>
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
                    <div class="flex flex-col gap-2">
                        <label for="password_confirmation">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            wire:model="password_confirmation"
                            class="p-2 border border-zinc-300 rounded-lg"
                            required
                        >
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label for="income">Income</label>
                            <input
                                type="number"
                                id="income"
                                name="income"
                                wire:model="income"
                                class="p-2 border border-zinc-300 rounded-lg"
                                required
                            >
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="frequency">Frequency</label>
                            <select
                                id="frequency"
                                name="frequency"
                                wire:model="frequency"
                                class="p-2 border bg-white border-zinc-300 rounded-lg"
                                required
                            >
                                <option value="W">Weekly</option>
                                <option value="B">Bi-Weekly</option>
                                <option value="M">Monthly</option>
                                <option value="Y">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-zinc-800 text-zinc-50 rounded-lg border border-zinc-900 w-full"
                        >Register</button>
                    </div>
                </form>
                <div class="text-xs sm:text-sm">
                    <a
                        href="{{ route('login') }}"
                        class="text-secondary font-bold"
                    >Already have an account? Login</a>
                </div>
            </div>
        </div>

        <x-guest-footer />
    </div>
</div>
