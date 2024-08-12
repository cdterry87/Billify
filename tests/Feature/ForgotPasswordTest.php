<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\ForgotPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_request_password_reset_link()
    {
        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', 'notauser@example.com')
            ->call('submit')
            ->assertHasErrors(['email'])
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasNoErrors();
    }
}
