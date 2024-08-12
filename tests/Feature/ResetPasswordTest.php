<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Livewire\ResetPassword;
use App\Models\PasswordResetToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_resetting_password()
    {
        $user = User::factory()->create();

        $token = Str::uuid();
        $passwordResetToken = PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Test invalid token
        Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
            ->assertStatus(404);

        //Test valid token



        Livewire::test(ResetPassword::class, [
            'token' => $token
        ])
            ->assertSet('email', $passwordResetToken->email)
            ->assertSet('token', $passwordResetToken->token)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
            'token' => $token,
        ]);
    }
}
