<?php

namespace App\Traits;

use Livewire\Attributes\On;
use Illuminate\Support\Facades\Session;

trait WithAlerts
{
    public function setAlert($message, $type = 'success'): void
    {
        Session::flash('alert-type', $type);
        Session::flash('alert-message', $message);
    }

    #[On('setModalAlert')]
    public function setModalAlert($message, $type = 'success'): void
    {
        Session::flash('modal-alert-type', $type);
        Session::flash('modal-alert-message', $message);
    }

    #[On('resetAlerts')]
    public function resetAlerts(): void
    {
        Session::forget('alert-type');
        Session::forget('alert-message');
        Session::forget('modal-alert-type');
        Session::forget('modal-alert-message');
    }
}
