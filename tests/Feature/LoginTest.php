<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Login;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_logging_in()
    {
        $password = 'password1';
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make($password),
        ]);

        // Invalid user
        Livewire::test(Login::class)
            ->set('email', 'baduser@example.com')
            ->set('password', $password)
            ->call('submit')
            ->assertRedirect(route('login'));

        // Valid user
        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', $password)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));
    }
}
