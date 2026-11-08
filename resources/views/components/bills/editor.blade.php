<?php

use App\Enums\BillCategory;
use App\Livewire\Forms\BillForm;
use App\Models\Bill;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The add and edit bill modal. Opened by an `edit-bill` event carrying an ID, or null for a new bill.
 */
new class extends Component
{
    public BillForm $form;

    #[Locked]
    public ?int $billId = null;

    public bool $saved = false;

    #[On('edit-bill')]
    public function open(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->saved = false;

        if ($id === null) {
            $this->startNew();
        } else {
            $bill = $this->findBill($id);
            $this->authorize('update', $bill);

            $this->billId = $bill->id;
            $this->form->fillFrom($bill);
        }

        $this->dispatch('open-modal', name: 'bill-editor');
    }

    public function startNew(): void
    {
        $this->resetErrorBag();
        $this->form->reset();
        $this->billId = null;
        $this->saved = false;
    }

    public function save(): void
    {
        $bill = null;

        if ($this->billId !== null) {
            $bill = $this->findBill($this->billId);
            $this->authorize('update', $bill);
        } elseif (auth()->user()->bills()->count() >= config('billify.max_bills')) {
            $this->addError('limit', 'You can have up to '.config('billify.max_bills').' bills. Delete one to add another.');

            return;
        }

        $bill = $this->form->save(auth()->user(), $bill);

        $this->billId = $bill->id;
        $this->saved = true;
        $this->dispatch('bills-changed');
    }

    public function delete(): void
    {
        if ($this->billId === null) {
            return;
        }

        $bill = $this->findBill($this->billId);
        $this->authorize('delete', $bill);

        $bill->delete();
        $this->startNew();

        $this->dispatch('close-modal', name: 'bill-editor');
        $this->dispatch('bills-changed');
        $this->dispatch('toast', type: 'success', message: "\"{$bill->name}\" was deleted.");
    }

    private function findBill(int $id): Bill
    {
        return auth()->user()->bills()->findOrFail($id);
    }
};
?>

<div>
    <x-ui.modal name="bill-editor" :title="$billId ? 'Edit bill' : 'Add bill'">
        <div x-data="confirmable" x-on:open-modal.window="confirming = false" class="stack">
            <div x-show="confirming" x-cloak class="stack" role="group" aria-labelledby="bill-editor-confirm" x-on:keydown.escape.prevent.stop="cancel">
                <p id="bill-editor-confirm">Are you sure you want to delete <strong>{{ $form->name }}</strong>? This cannot be undone.</p>
                <div class="cluster">
                    <x-ui.button variant="danger" icon="trash" wire:click="delete">Yes, delete</x-ui.button>
                    <x-ui.button variant="ghost" x-ref="cancel" x-on:click="cancel">No, cancel</x-ui.button>
                </div>
            </div>

            <form x-show="!confirming" class="stack" wire:submit="save" novalidate>
                {{-- The live region stays in the page so screen readers announce the message when it appears. --}}
                <div class="live-region" role="status">
                    @if ($saved)
                        <p class="notice">Bill saved successfully!</p>
                    @endif
                </div>
                @error('limit')<p class="notice notice--error" role="alert">{{ $message }}</p>@enderror

                <x-ui.field name="form.name" label="Name" maxlength="64" required wire:model="form.name" />
                <x-ui.field name="form.category" label="Category" as="select" placeholder="Select a category" :options="BillCategory::options()" required wire:model="form.category" />
                <x-ui.field name="form.description" label="Description" as="textarea" maxlength="255" wire:model="form.description" />

                <div class="form-row">
                    <x-ui.field name="form.amount" label="Amount ($)" inputmode="decimal" placeholder="0.00" required wire:model="form.amount" />
                    <x-ui.field name="form.due_day" label="Day of month" type="number" min="1" max="31" hint="Days past a month's end fall on its last day." required wire:model="form.due_day" />
                </div>

                <fieldset class="months" aria-describedby="{{ collect(['bill-months-hint', $errors->has('form.months') ? 'bill-months-error' : null, $errors->has('form.months.*') ? 'bill-months-item-error' : null])->filter()->implode(' ') }}">
                    <legend class="field__label">Monthly recurrence</legend>
                    <p class="field__hint" id="bill-months-hint">Leave every month unticked for a bill due every month, or tick only the months it applies to.</p>
                    <div class="months__grid">
                        @foreach (range(1, 12) as $month)
                            <x-ui.checkbox name="form.months" id="bill-month-{{ $month }}" value="{{ $month }}" :label="date('F', mktime(0, 0, 0, $month, 1))" wire:model="form.months" />
                        @endforeach
                    </div>
                    @error('form.months')<p class="field__error" id="bill-months-error">{{ $message }}</p>@enderror
                    @error('form.months.*')<p class="field__error" id="bill-months-item-error">{{ $message }}</p>@enderror
                </fieldset>

                <div class="modal__actions">
                    @if ($billId)
                        <x-ui.button variant="ghost" icon="trash" class="modal__actions-start" x-ref="trigger" x-on:click="ask">Delete</x-ui.button>
                        <x-ui.button variant="secondary" icon="plus" wire:click="startNew">New bill</x-ui.button>
                    @endif
                    <x-ui.button type="submit" wire:loading.attr="disabled">Save bill</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
