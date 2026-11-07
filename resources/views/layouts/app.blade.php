<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="app">
        <a class="skip-link" href="#main">Skip to content</a>
        <x-ui.header>
            {{ $actions ?? '' }}
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" icon="logout" label="Log out" />
                </form>
            @endauth
        </x-ui.header>
        <main id="main" class="app__main">{{ $slot }}</main>
        <x-ui.footer />
        <x-ui.toasts />
        @livewireScripts
    </body>
</html>
