<fieldset class="theme-toggle" x-data>
    <legend class="visually-hidden">Theme</legend>
    <div class="theme-toggle__options">
        @foreach (['system' => ['System', 'system'], 'light' => ['Light', 'sun'], 'dark' => ['Dark', 'moon']] as $value => [$label, $icon])
            <label class="theme-toggle__option" title="{{ $label }}">
                <input
                    class="visually-hidden"
                    type="radio"
                    name="theme"
                    value="{{ $value }}"
                    x-bind:checked="$store.theme.value === @js($value)"
                    x-on:change="$store.theme.set(@js($value))"
                >
                <x-ui.icon :name="$icon" size="sm" />
                <span class="visually-hidden">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
