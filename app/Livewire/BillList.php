<?php

namespace App\Livewire;

use Livewire\Component;

class BillList extends Component
{
    public function render()
    {
        $bills = auth()->user()->bills()->orderBy('day')->get();

        return view('livewire.bill-list', [
            'bills' => $bills,
        ]);
    }
}
