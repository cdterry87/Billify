{{-- Each bar: ['label' => string, 'value' => number, 'display' => string, 'tone' => 'positive'|'negative'|null] --}}
@props(['title', 'bars' => [], 'empty' => 'Nothing to show yet.'])

@php
    $max = collect($bars)->max('value') ?: 0;
@endphp

<figure {{ $attributes->class('chart') }}>
    <figcaption class="chart__title">{{ $title }}</figcaption>

    @if ($max > 0)
        <ul class="chart__bars">
            @foreach ($bars as $bar)
                <li @class(['chart__bar', 'chart__bar--'.($bar['tone'] ?? '') => filled($bar['tone'] ?? null)])>
                    <span class="chart__value">{{ $bar['display'] }}</span>
                    <span class="chart__track" aria-hidden="true">
                        <span class="chart__fill" style="--bar-height: {{ round($bar['value'] / $max * 100, 2) }}%"></span>
                    </span>
                    <span class="chart__label">{{ $bar['label'] }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="chart__empty">{{ $empty }}</p>
    @endif
</figure>
