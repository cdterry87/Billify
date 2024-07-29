<div class="h-screen text-white">
    {{-- Background Video --}}
    <div class="login--video-container video-overlay">
        <video
            autoplay
            muted
            loop
            id="login--background-video"
        >
            <source
                src="{{ asset('videos/budget.mp4') }}"
                type="video/mp4"
            >
            Your browser does not support the video tag.
        </video>
    </div>

    {{-- Login Form --}}
    <div class="flex flex-col gap-8 items-center justify-center h-full mx-4">
        <div class="flex flex-col items-center gap-6 p-8 bg-zinc-100 text-zinc-800 w-full sm:w-120 rounded-lg">
            <h1 class="text-3xl font-bold">{{ config('app.name') }}</h1>
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
                <button
                    type="submit"
                    class="px-4 py-2 bg-zinc-700 text-zinc-50 rounded-lg border border-zinc-800"
                >Login</button>
            </form>
            <div class="flex flex-col sm:flex-row gap-2 text-xs">
                <a
                    href="{{ route('register') }}"
                    class="text-primary"
                >Create an Account</a>
                <span class="hidden sm:block">|</span>
                <a
                    href="{{ route('forgot-password') }}"
                    class="text-primary"
                >Forgot Password?</a>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="flex flex-col items-center gap-2 text-xs pb-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center text-center sm:gap-2">
                <p>Copyright © {{ date('Y') }}. All rights reserved.</p>
                <span class="hidden sm:block">|</span>
                <a
                    href="{{ route('privacy-policy') }}"
                    class="font-bold text-primary"
                >Privacy Policy</a>
            </div>
            <div class="text-center italic">
                Developed by
                <a
                    href="https://chaseterry.com"
                    class="font-bold text-primary"
                >
                    Chase Terry
                </a>
            </div>
        </div>
    </div>
</div>
