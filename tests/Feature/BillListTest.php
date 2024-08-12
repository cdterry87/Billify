<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\BillList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BillListTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_correctly_and_can_delete()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Render without bills
        Livewire::test(BillList::class)
            ->assertSee('empty-results--message')
            ->assertDontSee('bill-list-item--container');

        // Render with bills
        // Create a bill (and make sure it's not for any specific months)
        Bill::factory(5)->create([
            'user_id' => $user->id,
            'january' => false,
            'february' => false,
            'march' => false,
            'april' => false,
            'may' => false,
            'june' => false,
            'july' => false,
            'august' => false,
            'september' => false,
            'october' => false,
            'november' => false,
            'december' => false,
        ]);

        Livewire::test(BillList::class)
            ->assertDontSee('empty-results--message')
            ->assertSee('bill-list-item--container')
            ->set('filterSearch', 'sd89f7as0d9f87sa9f8sdf9sd7f') // set a random search filter to return no results
            ->assertDontSee('bill-list-item--container')
            ->assertDontSee('empty-results--message')
            ->assertSee('empty-filters--message')
            ->set('filterSearch', null)
            ->set('filterShowing', '')
            ->set('filterCategory', 'entertainment')
            ->assertSee('(Filtered)')
            ->call('resetFilters')
            ->assertDontSee('(Filtered)')
            ->assertSet('filterSearch', null)
            ->assertSet('filterCategory', null)
            ->assertSet('filterShowing', 'current');

        // Get a bill
        $bill = Bill::query()
            ->where('user_id', $user->id)
            ->first();

        // Delete the bill
        Livewire::test(BillList::class)
            ->assertSee($bill->name)
            ->call('deleteBill', $bill->id)
            ->assertDontSee($bill->name);

        // Assert bill is missing
        $this->assertDatabaseMissing('bills', [
            'id' => $bill->id,
        ]);
    }
}
