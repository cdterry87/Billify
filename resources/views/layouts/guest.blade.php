<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="guest">
        <a class="skip-link" href="#main">Skip to content</a>
        <div class="guest__bar">
            <x-ui.theme-toggle />
        </div>
        <main id="main" class="guest__main">
            <x-ui.logo />
            <div @class(['guest__panel', 'guest__panel--wide' => $wide ?? false])>{{ $slot }}</div>
        </main>
        <x-ui.footer />
        <x-ui.toasts />
        @livewireScripts
    </body>
</html>
