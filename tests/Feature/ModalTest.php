<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Modal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_and_close_modals()
    {
        $user = User::factory()->create();
        $bill = Bill::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Modal::class)
            ->call('open', 'Bill Form', 'bill-form', ['modelId' => $bill->id])
            ->assertSet('title', 'Bill Form')
            ->assertSet('component', 'bill-form')
            ->assertSet('params', ['modelId' => $bill->id])
            ->assertSet('isOpen', true)
            ->call('close')
            ->assertSet('title', '')
            ->assertSet('component', '')
            ->assertSet('params', [])
            ->assertSet('isOpen', false);
    }
}
