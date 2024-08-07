<?php

namespace App\Livewire;

use App\Traits\WithAlerts;
use Livewire\Component;

class Modal extends Component
{
    use WithAlerts;

    public $isOpen = false;
    public $title = '';
    public $component = '';
    public $params = [];

    protected $listeners = [
        'openModal' => 'open',
        'closeModal' => 'close',
    ];

    public function open($title, $component, $params = [])
    {
        $this->title = $title;
        $this->component = $component;
        $this->params = $params;
        $this->isOpen = true;
    }

    public function close()
    {
        $this->title = '';
        $this->params = [];
        $this->component = '';
        $this->isOpen = false;

        $this->resetAlerts();
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
