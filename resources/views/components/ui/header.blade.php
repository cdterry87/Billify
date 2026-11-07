<header class="site-header">
    <div class="site-header__inner">
        <x-ui.logo />
        <div class="site-header__actions">
            <x-ui.theme-toggle />
            {{ $slot }}
        </div>
    </div>
</header>
