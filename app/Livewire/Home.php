<?php

namespace App\Livewire;

use App\Traits\WithAlerts;
use Livewire\Component;

class Home extends Component
{
    use WithAlerts;

    public function render()
    {
        return view('livewire.home');
    }
}
