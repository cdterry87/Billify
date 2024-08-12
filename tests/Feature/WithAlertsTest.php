<?php

namespace Tests\Feature;

use App\Traits\WithAlerts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithAlertsTest extends TestCase
{
    use RefreshDatabase;
    use WithAlerts;

    public function test_setting_and_resetting_alerts()
    {
        $this->setAlert('This is an error message', 'error');
        $this->setModalAlert('This is a modal warning message', 'warning');

        $this->assertEquals('error', session('alert-type'));
        $this->assertEquals('This is an error message', session('alert-message'));
        $this->assertEquals('warning', session('modal-alert-type'));
        $this->assertEquals('This is a modal warning message', session('modal-alert-message'));

        $this->resetAlerts();

        $this->assertNull(session('alert-type'));
        $this->assertNull(session('alert-message'));
        $this->assertNull(session('modal-alert-type'));
        $this->assertNull(session('modal-alert-message'));
    }
}
