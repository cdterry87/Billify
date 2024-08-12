<div class="h-screen text-white">
    <x-background-video assetPath="videos/budget.mp4" />

    <div class="flex flex-col gap-4 items-center justify-center h-full mx-4 my-8">
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

                <x-alert type="info">
                    <div class="flex flex-col gap-2">
                        <p class="text-sm">
                            To demo the application, use the following credentials:
                        </p>
                        <div class="flex flex-col">
                            <p class="text-sm">
                                Email: <strong class="underline">demo@example.com</strong>
                            </p>
                            <p class="text-sm">
                                Password: <strong class="underline">password1</strong>
                            </p>
                        </div>
                        <p class="text-xs italic">NOTE: Demo user and data are reset daily.</p>
                    </div>
                </x-alert>

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

        <x-footer />
    </div>
</div>
