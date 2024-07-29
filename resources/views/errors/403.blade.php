<x-layouts.error
    icon="icons.error-403"
    message="Sorry, you are not authorized to access this page."
>
    <a
        href="{{ route('home') }}"
        class="btn btn-primary mt-6"
    >
        Return Home
    </a>
</x-layouts.error>
