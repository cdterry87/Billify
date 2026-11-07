@props(['title' => null, 'actions' => null])

<section {{ $attributes->class('card') }}>
    @if ($title || $actions)
        <header class="card__header">
            @if ($title)<h2 class="card__title">{{ $title }}</h2>@endif
            @if ($actions)<div class="cluster">{{ $actions }}</div>@endif
        </header>
    @endif
    {{ $slot }}
</section>
