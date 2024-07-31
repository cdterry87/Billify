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
