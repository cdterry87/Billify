<x-layouts.error
    icon="icons.error-403"
    message="Sorry, you are not authorized to access this page."
>
    <a
        href="{{ route('home') }}"
        class="flex items-center gap-2 bg-zinc-800 text-zinc-50 px-4 py-2 hover:brightness-125 transition duration-300 ease-in-out rounded-lg"
    >
        <x-heroicon-c-arrow-left class="w-6 h-6" />
        Return Home
    </a>
</x-layouts.error>
