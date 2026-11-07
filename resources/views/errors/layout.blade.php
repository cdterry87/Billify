{{-- Error pages stay simple: no Livewire, no session data, and no database, so they render even when those fail. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head', ['title' => trim($__env->yieldContent('title'))])
    </head>
    <body class="guest">
        <div class="guest__bar"></div>
        <main class="guest__main">
            <x-ui.logo />
            <div class="guest__panel">
                <x-ui.card class="error-page">
                    <p class="error-page__code">@yield('code')</p>
                    <h1 class="error-page__title">@yield('title')</h1>
                    <p class="error-page__message">@yield('message')</p>
                    @hasSection('action')
                        @yield('action')
                    @else
                        <x-ui.button :href="url('/')" block>Back to {{ config('app.name') }}</x-ui.button>
                    @endif
                    <p class="error-page__note">{{ config('app.name') }} is a portfolio application that is not intended for public use.</p>
                </x-ui.card>
            </div>
        </main>
    </body>
</html>
