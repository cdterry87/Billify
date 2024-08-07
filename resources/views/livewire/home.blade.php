<div class="flex flex-col gap-4">
    {{-- Header --}}
    {{-- Logo - Theme - User - Logout --}}
    <div class="flex items-center justify-between gap-4 py-4 px-4 md:px-8">
        <a href="{{ route('home') }}">
            <h1
                class="font-bold text-3xl sm:text-4xl logo text-primary hover:brightness-90 transition duration-200 ease-in-out">
                {{ config('app.name') }}
            </h1>
        </a>
        <div class="flex items-center gap-4 text-sm sm:text-base">
            <a
                href="#"
                alt="My Profile"
                title="My Profile"
                class="flex items-center gap-1 font-bold hover:brightness-125 transition duration-200 ease-in-out"
            >
                <x-heroicon-s-user-circle class="h-8 w-8" />
                <span class="hidden sm:block">My Profile</span>
            </a>
            <a
                href="#"
                alt="Logout"
                title="Logout"
                class="flex items-center gap-1 font-bold text-red-800 hover:brightness-125 transition duration-200 ease-in-out"
            >
                <x-heroicon-s-arrow-left-end-on-rectangle class="h-8 w-8" />
                <span class="hidden sm:block">Logout</span>
            </a>
        </div>
    </div>

    <div class="mx-4 md:mx-8">
        {{-- User Settings --}}
        {{-- Change name, email, income, frequency --}}
        {{-- Change password --}}
        {{-- Delete account --}}


        {{-- Add Bill Form --}}



        {{-- Bill Charts --}}



        {{-- Bill List --}}
        {{-- Add Bill Button --}}
        <x-card title="My Bills">
            <x-slot name="primaryAction">
                <x-actions.button
                    label="Add Bill"
                    icon="heroicon-c-plus-circle"
                />
            </x-slot>
        </x-card>
    </div>
</div>
