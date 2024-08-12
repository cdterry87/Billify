<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Register;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register()
    {
        Livewire::test(Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'jdoe@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('income', 6000)
            ->set('frequency', '12')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'income' => 6000,
            'frequency' => '12',
        ]);

        // Try to create another account with the same name. It should error out.
        Livewire::test(Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'jdoe@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('income', 6000)
            ->set('frequency', '12')
            ->call('submit')
            ->assertHasErrors();
    }
}
