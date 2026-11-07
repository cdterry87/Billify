<?php

use App\Support\CategoryTotal;
use App\Support\CategoryTotals;
use App\Support\IncomeSummary;
use App\Support\Money;
use App\Support\UpcomingBill;
use App\Support\UpcomingBills;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    #[On('bills-changed')]
    public function refresh(): void
    {
        unset($this->summary, $this->upcoming, $this->categories);
    }

    #[Computed]
    public function summary(): IncomeSummary
    {
        return IncomeSummary::for(auth()->user());
    }

    /**
     * @return Collection<int, UpcomingBill>
     */
    #[Computed]
    public function upcoming(): Collection
    {
        return UpcomingBills::for(auth()->user());
    }

    /**
     * @return Collection<int, CategoryTotal>
     */
    #[Computed]
    public function categories(): Collection
    {
        return CategoryTotals::for(auth()->user(), auth()->user()->now()->month);
    }

    public function dueLabel(UpcomingBill $upcoming): string
    {
        return match ($upcoming->daysUntil) {
            0 => 'due today',
            1 => 'due tomorrow',
            default => "due in {$upcoming->daysUntil} days",
        };
    }
};
?>

@php
    $summary = $this->summary;
    $percent = $summary->billsVsIncomePercent();
    $remainder = $summary->remainderCents();
@endphp

<div class="stack stack--lg">
    <header class="page-header">
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <p class="muted">Here's where {{ auth()->user()->now()->format('F') }} stands.</p>
    </header>

    @if ($this->upcoming->isNotEmpty())
        <section class="due-soon" aria-labelledby="due-soon-title">
            <h2 class="due-soon__title" id="due-soon-title">
                <x-ui.icon name="bell" />
                You have bills due soon
            </h2>
            <ul class="due-soon__list" role="list">
                @foreach ($this->upcoming as $upcoming)
                    <li class="due-soon__item">
                        <span><strong>{{ $upcoming->bill->name }}</strong> {{ $this->dueLabel($upcoming) }}</span>
                        <span class="due-soon__meta">
                            <time datetime="{{ $upcoming->dueDate->toDateString() }}">{{ $upcoming->dueDate->format('D, M j') }}</time>
                            · {{ Money::format($upcoming->bill->amount_cents) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section aria-label="Income summary" class="grid-tiles">
        <x-ui.stat-tile label="Annual income" :value="Money::format($summary->annualCents)" />
        <x-ui.stat-tile label="Monthly income" :value="Money::format($summary->monthlyCents)" />
        <x-ui.stat-tile label="Bi-weekly income" :value="Money::format($summary->biWeeklyCents)" />
        <x-ui.stat-tile label="Weekly income" :value="Money::format($summary->weeklyCents)" />
        <x-ui.stat-tile label="Monthly remainder" :value="Money::format($remainder)" :tone="$remainder < 0 ? 'negative' : 'positive'" />
        <x-ui.stat-tile label="Bills vs Income" :value="$percent === null ? 'N/A' : number_format($percent, 2).'%'" />
    </section>

    <div class="grid-split">
        <x-ui.card>
            <x-ui.chart-bar
                title="Income vs Bills"
                empty="Add your income and bills to compare them."
                :bars="[
                    ['label' => 'Income', 'value' => $summary->monthlyCents, 'display' => Money::format($summary->monthlyCents), 'tone' => 'positive'],
                    ['label' => 'Bills', 'value' => $summary->monthlyBillsCents, 'display' => Money::format($summary->monthlyBillsCents), 'tone' => 'negative'],
                ]"
            />
        </x-ui.card>
        <x-ui.card>
            <x-ui.chart-bar
                title="Top bill categories"
                empty="No bills are due this month."
                :bars="$this->categories->map(fn ($total) => [
                    'label' => $total->category->label(),
                    'value' => $total->totalCents,
                    'display' => Money::format($total->totalCents),
                ])->all()"
            />
        </x-ui.card>
    </div>

    <x-ui.card title="My monthly bills">
        <x-slot:actions>
            <x-ui.button icon="plus" x-on:click="$dispatch('edit-bill', { id: null })">Add bill</x-ui.button>
        </x-slot:actions>

        <livewire:bills.list />
    </x-ui.card>

    <livewire:bills.editor />
</div>
