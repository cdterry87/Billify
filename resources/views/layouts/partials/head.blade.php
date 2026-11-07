<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="light dark">
<title>{{ filled($title ?? null) ? $title.' · '.config('app.name') : config('app.name') }}</title>
<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#3f51c9">
{{-- Runs before first paint so a saved theme never flashes the wrong colours. --}}
<script>
    try {
        const theme = localStorage.getItem('billify-theme');
        if (theme === 'light' || theme === 'dark') {
            document.documentElement.dataset.theme = theme;
        }
    } catch {}
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
