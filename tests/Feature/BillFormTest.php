<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\BillForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BillFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(BillForm::class)
            ->set('name', 'Bill Name')
            ->set('category', 'entertainment')
            ->set('description', 'Entertainment bill')
            ->set('amount', 100)
            ->set('day', 1)
            ->set('september', true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('bills', [
            'user_id' => $user->id,
            'name' => 'Bill Name',
            'category' => 'entertainment',
            'description' => 'Entertainment bill',
            'amount' => 100,
            'day' => 1,
        ]);
    }

    public function test_can_edit_and_delete()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $bill = Bill::factory()->create([
            'user_id' => $user->id,
        ]);

        // Test editing an invalid bill
        Livewire::test(BillForm::class)
            ->call('edit', '9sdf9s8dfsd0f')
            ->assertSet('modelId', null)
            ->assertRedirect(route('home'));

        // Test editing a valid bill
        Livewire::test(BillForm::class)
            ->call('edit', $bill->id)
            ->assertSet('modelId', $bill->id)
            ->assertSet('name', $bill->name)
            ->assertSet('category', $bill->category)
            ->assertSet('description', $bill->description)
            ->assertSet('amount', $bill->amount)
            ->assertSet('day', $bill->day)
            ->assertSet('january', $bill->january)
            ->assertSet('february', $bill->february)
            ->assertSet('march', $bill->march)
            ->assertSet('april', $bill->april)
            ->assertSet('may', $bill->may)
            ->assertSet('june', $bill->june)
            ->assertSet('july', $bill->july)
            ->assertSet('august', $bill->august)
            ->assertSet('september', $bill->september)
            ->assertSet('october', $bill->october)
            ->assertSet('november', $bill->november)
            ->assertSet('december', $bill->december)
            ->call('delete');

        $this->assertDatabaseMissing('bills', [
            'id' => $bill->id,
        ]);

        // Test deleting an invalid bill
        Livewire::test(BillForm::class)
            ->set('modelId', 'asf987sdf8sdfsfd')
            ->call('delete')
            ->assertRedirect(route('home'));
    }
}
