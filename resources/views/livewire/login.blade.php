<div class="h-screen">
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


    {{-- Copyright --}}
    <div class="flex flex-col gap-2 text-xs pb-8">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
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
