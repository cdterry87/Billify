@props(['variant' => 'primary', 'size' => null, 'icon' => null, 'href' => null, 'type' => 'button', 'block' => false, 'label' => null])

@php
    $classes = ['btn', "btn--{$variant}", 'btn--sm' => $size === 'sm', 'btn--block' => $block, 'btn--icon' => $icon && $slot->isEmpty()];
@endphp

@if ($href)
    <a {{ $attributes->class($classes)->merge(['href' => $href, 'aria-label' => $label]) }}>
        @if ($icon)<x-ui.icon :name="$icon" />@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class($classes)->merge(['type' => $type, 'aria-label' => $label]) }}>
        @if ($icon)<x-ui.icon :name="$icon" />@endif
        {{ $slot }}
    </button>
@endif
