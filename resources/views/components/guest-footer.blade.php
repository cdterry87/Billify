<div class="flex flex-col items-center gap-2 text-xs pb-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center text-center sm:gap-2">
        <p>Copyright © {{ date('Y') }}. All rights reserved.</p>
        <span class="hidden sm:block">|</span>
        <x-inputs.link
            class=" text-primary"
            href="{{ route('privacy-policy') }}"
            label="Privacy Policy"
        />
    </div>
    <div class="text-center italic">
        Developed by
        <x-inputs.link
            aria-label="Visit my website!"
            class="text-primary"
            href="https://chaseterry.com"
            label="Chase Terry"
            target="_blank"
            title="Visit my website!"
        />
    </div>
</div>
