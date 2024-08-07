<div class="flex flex-col items-center gap-2 text-xs pb-8 w-full">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center text-center sm:gap-2">
        <p>Copyright © {{ date('Y') }}. All rights reserved.</p>
        <span class="hidden sm:block">|</span>
        <x-actions.link
            class=" text-primary"
            href="{{ route('privacy-policy') }}"
            label="Privacy Policy"
        />
    </div>
    <div class="text-center italic flex items-center gap-1">
        Developed by
        <x-actions.link
            aria-label="Visit my website!"
            class="text-primary"
            href="https://chaseterry.com"
            label="Chase Terry"
            target="_blank"
            title="Visit my website!"
        />
    </div>
</div>
