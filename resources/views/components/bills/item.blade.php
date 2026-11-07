@props(['bill'])

<li {{ $attributes->class('bill-item') }} x-data="confirmable">
    <div class="bill-item__main" x-show="!confirming">
        <div class="bill-item__body">
            <p class="bill-item__name">{{ $bill->name }}</p>
            @if ($bill->description)<p class="bill-item__description">{{ $bill->description }}</p>@endif
            <div class="bill-item__chips">
                <x-ui.chip>{{ $bill->category->label() }}</x-ui.chip>
                <x-ui.chip>Day {{ $bill->due_day }}</x-ui.chip>
                @if ($bill->months === null)
                    <x-ui.chip>Every month</x-ui.chip>
                @else
                    @foreach ($bill->months as $month)
                        <x-ui.chip tone="accent">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</x-ui.chip>
                    @endforeach
                @endif
            </div>
        </div>
        <span class="bill-item__amount">{{ App\Support\Money::format($bill->amount_cents) }}</span>
        <div class="bill-item__actions">
            <x-ui.button variant="ghost" size="sm" icon="pencil" :label="'Edit '.$bill->name" wire:click="$dispatch('edit-bill', { id: {{ $bill->id }} })" />
            <x-ui.button variant="ghost" size="sm" icon="trash" :label="'Delete '.$bill->name" x-ref="trigger" x-on:click="ask" />
        </div>
    </div>

    <div class="bill-item__confirm" x-show="confirming" x-cloak role="group" aria-labelledby="bill-{{ $bill->id }}-confirm" x-on:keydown.escape.stop="cancel">
        <p id="bill-{{ $bill->id }}-confirm">Delete <strong>{{ $bill->name }}</strong>?</p>
        <div class="cluster">
            <x-ui.button variant="danger" size="sm" wire:click="delete({{ $bill->id }})">Yes, delete</x-ui.button>
            <x-ui.button variant="ghost" size="sm" x-ref="cancel" x-on:click="cancel">No, cancel</x-ui.button>
        </div>
    </div>
</li>
