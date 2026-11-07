@props(['label', 'value', 'tone' => null])

<div {{ $attributes->class(['stat-tile', 'stat-tile--'.$tone => filled($tone)]) }}>
    <span class="stat-tile__label">{{ $label }}</span>
    <span class="stat-tile__value">{{ $value }}</span>
</div>
