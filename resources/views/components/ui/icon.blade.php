{{-- Names are the files in ui/icons, from Heroicons (MIT, see LICENSE-heroicons.txt there). Never pass user input. --}}
@props(['name', 'size' => null])

<svg {{ $attributes->class(['icon', 'icon--sm' => $size === 'sm']) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
    @include("components.ui.icons.{$name}")
</svg>
