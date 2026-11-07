<?php

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Support\IncomeSummary;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The user's bills with search and filters. Filter state lives in the URL so it survives a reload.
 */
new class extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'current')]
    public string $showing = 'current';

    #[Url(except: '')]
    public string $category = '';

    public function updated(): void
    {
        // Ignore unknown values from a hand-edited URL rather than failing.
        if (! in_array($this->showing, ['current', 'all'], true)) {
            $this->showing = 'current';
        }

        if (BillCategory::tryFrom($this->category) === null) {
            $this->category = '';
        }
    }

    public function mount(): void
    {
        $this->updated();
    }

    #[On('bills-changed')]
    public function refresh(): void
    {
        unset($this->bills, $this->summary, $this->hasAnyBills);
    }

    /**
     * @return Collection<int, Bill>
     */
    #[Computed]
    public function bills(): Collection
    {
        $search = trim($this->search);

        return auth()->user()->bills()
            ->when($this->showing === 'current', fn ($query) => $query->dueInMonth(auth()->user()->now()->month))
            ->when($search !== '', fn ($query) => $query->whereLike('name', '%'.addcslashes($search, '%_\\').'%'))
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->orderBy('due_day')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function filtered(): bool
    {
        return trim($this->search) !== '' || $this->category !== '';
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'showing', 'category');
    }

    public function delete(int $id): void
    {
        $bill = auth()->user()->bills()->findOrFail($id);
        $this->authorize('delete', $bill);

        $bill->delete();

        $this->dispatch('bills-changed');
        $this->dispatch('toast', type: 'success', message: "\"{$bill->name}\" was deleted.");
    }

    /**
     * Income and remainder only make sense against the unfiltered current month.
     */
    #[Computed]
    public function summary(): ?IncomeSummary
    {
        return $this->showing === 'current' && ! $this->filtered
            ? IncomeSummary::for(auth()->user())
            : null;
    }

    #[Computed]
    public function monthLabel(): string
    {
        return auth()->user()->now()->format('F Y');
    }

    #[Computed]
    public function hasAnyBills(): bool
    {
        return $this->bills->isNotEmpty() || auth()->user()->bills()->exists();
    }
};
?>

<div class="bill-list">
    <div class="bill-list__filters" role="search">
        <x-ui.field name="search" label="Search" type="search" placeholder="Bill name" wire:model.live.debounce.500ms="search" />
        <x-ui.field name="showing" label="Showing" as="select" :options="['current' => 'Current month only', 'all' => 'All months']" wire:model.live="showing" />
        <x-ui.field name="category" label="Category" as="select" placeholder="All categories" :options="BillCategory::options()" wire:model.live="category" />
        <x-ui.button variant="ghost" icon="reset" class="bill-list__reset" wire:click="resetFilters">Reset</x-ui.button>
    </div>

    <div class="bill-list__meta">
        <h3 class="bill-list__heading">
            {{ $showing === 'current' ? "For {$this->monthLabel}" : 'For all months' }}
            @if ($this->filtered)<span class="muted">(Filtered)</span>@endif
        </h3>
        <span class="muted" aria-live="polite">{{ trans_choice(':count result|:count results', $this->bills->count()) }}</span>
    </div>

    @if ($this->bills->isEmpty())
        <div class="empty-state">
            @if (! $this->hasAnyBills)
                <p class="empty-state__title">You have not added any bills yet.</p>
                <p class="muted">Use "Add bill" above to add your first one.</p>
            @elseif ($this->filtered || $showing !== 'current')
                <p class="empty-state__title">No results were found.</p>
                <p class="muted">Try a different filter.</p>
            @else
                <p class="empty-state__title">No bills due in {{ $this->monthLabel }}.</p>
                <p class="muted">Use "Add bill" above to add one, or show all months.</p>
            @endif
        </div>
    @else
        <ul class="bill-list__items" role="list">
            @foreach ($this->bills as $bill)
                <x-bills.item :$bill wire:key="bill-{{ $bill->id }}" />
            @endforeach
        </ul>

        <dl class="bill-totals">
            @if ($this->summary)
                <div class="bill-totals__row">
                    <dt>Monthly income</dt>
                    <dd>{{ App\Support\Money::format($this->summary->monthlyCents) }}</dd>
                </div>
            @endif
            <div class="bill-totals__row">
                <dt>Bills total</dt>
                <dd>{{ App\Support\Money::format($this->bills->sum('amount_cents')) }}</dd>
            </div>
            @if ($this->summary)
                <div @class(['bill-totals__row', 'bill-totals__row--strong', 'is-negative' => $this->summary->remainderCents() < 0, 'is-positive' => $this->summary->remainderCents() >= 0])>
                    <dt>Monthly remainder</dt>
                    <dd>{{ App\Support\Money::format($this->summary->remainderCents()) }}</dd>
                </div>
            @endif
        </dl>
    @endif
</div>
