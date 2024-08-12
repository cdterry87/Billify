<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\MyProfileForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyProfileFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(MyProfileForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'jdoe@example.com')
            ->set('income', 6000)
            ->set('frequency', '12')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'income' => 6000,
            'frequency' => '12',
        ]);
    }

    public function test_change_password()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Passwords don't match
        Livewire::test(MyProfileForm::class)
            ->set('password', 'password129837213')
            ->set('password_confirmation', '990sd8f0sfdsf')
            ->call('changePassword')
            ->assertHasErrors(['password']);

        // Passwords match
        Livewire::test(MyProfileForm::class)
            ->set('password', 'updatedpassword')
            ->set('password_confirmation', 'updatedpassword')
            ->call('changePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('updatedpassword', $user->fresh()->password));
    }

    public function test_delete_account()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(MyProfileForm::class)
            ->call('deleteAccount')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
