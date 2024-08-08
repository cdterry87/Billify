<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class BillList extends Component
{
    public $bills;

    public function mount()
    {
        $this->getBills();
    }

    public function render()
    {
        return view('livewire.bill-list');
    }

    #[On('refreshBillList')]
    public function getBills()
    {
        $this->bills = auth()->user()->bills()->orderBy('day')->get();
    }

    public function deleteBill($billId)
    {
        auth()->user()->bills()->where('id', $billId)->delete();

        $this->dispatch('setAlert', 'Bill deleted successfully!', 'error');

        $this->getBills();
    }
}
