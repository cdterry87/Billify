<?php

use App\Models\Bill;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 11, 15)->setTime(12, 0));
    $this->user = User::factory()->create(['income_cents' => 500_000, 'pay_frequency' => 12]);
    $this->actingAs($this->user);
});

it('lists bills due this month, sorted by day', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Rent', 'due_day' => 1, 'amount_cents' => 150_000]);
    Bill::factory()->for($this->user)->create(['name' => 'Phone', 'due_day' => 20, 'amount_cents' => 5_000, 'months' => [11]]);
    Bill::factory()->for($this->user)->create(['name' => 'Car tax', 'due_day' => 5, 'months' => [3]]);

    Livewire::test('bills.list')
        ->assertSeeInOrder(['For November 2026', '2 results', 'Rent', 'Phone'])
        ->assertDontSee('Car tax')
        ->assertSeeInOrder(['Monthly income', '$5,000.00', 'Bills total', '$1,550.00', 'Monthly remainder', '$3,450.00']);
});

it('shows all months when asked', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Car tax', 'months' => [3]]);

    Livewire::test('bills.list')
        ->set('showing', 'all')
        ->assertSee('For all months')
        ->assertSee('Car tax')
        ->assertSee('Mar')
        ->assertSee('Bills total')
        ->assertDontSee('Monthly remainder');
});

it('searches by name and filters by category', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Water', 'category' => 'utilities']);
    Bill::factory()->for($this->user)->create(['name' => 'Electric', 'category' => 'utilities']);
    Bill::factory()->for($this->user)->create(['name' => 'Netflix', 'category' => 'entertainment']);

    Livewire::test('bills.list')
        ->set('search', 'wat')
        ->assertSee('(Filtered)')
        ->assertSee('Water')
        ->assertDontSee('Electric')
        ->assertDontSee('Monthly remainder')
        ->set('search', '')
        ->set('category', 'utilities')
        ->assertSee('2 results')
        ->assertDontSee('Netflix');
});

it('treats search wildcards literally', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Rent']);

    Livewire::test('bills.list')->set('search', '%')->assertDontSee('Rent')->assertSee('No results were found.');
});

it('reads filters from the URL and ignores bad values', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Car tax', 'months' => [3], 'category' => 'transportation']);

    Livewire::withQueryParams(['showing' => 'all', 'category' => 'transportation', 'q' => 'car'])
        ->test('bills.list')
        ->assertSee('Car tax');

    Livewire::withQueryParams(['showing' => 'bogus', 'category' => 'bogus'])
        ->test('bills.list')
        ->assertSet('showing', 'current')
        ->assertSet('category', '');
});

it('resets the filters', function () {
    Livewire::test('bills.list')
        ->set(['search' => 'x', 'showing' => 'all', 'category' => 'food'])
        ->call('resetFilters')
        ->assertSet('search', '')
        ->assertSet('showing', 'current')
        ->assertSet('category', '');
});

it('shows the right empty state', function () {
    Livewire::test('bills.list')->assertSee('You have not added any bills yet.');

    Bill::factory()->for($this->user)->create(['name' => 'Car tax', 'months' => [3]]);

    Livewire::test('bills.list')->assertSee('No bills due in November 2026.');
    Livewire::test('bills.list')->set('search', 'zzz')->assertSee('No results were found.');
});

it('shows a negative remainder', function () {
    Bill::factory()->for($this->user)->create(['amount_cents' => 600_000]);

    Livewire::test('bills.list')->assertSee('-$1,000.00')->assertSeeHtml('is-negative');
});

it('deletes a bill', function () {
    $bill = Bill::factory()->for($this->user)->create(['name' => 'Gym']);

    Livewire::test('bills.list')
        ->call('delete', $bill->id)
        ->assertDispatched('bills-changed')
        ->assertDispatched('toast', type: 'success', message: '"Gym" was deleted.')
        ->assertDontSee('Gym');

    expect(Bill::count())->toBe(0);
});

it('cannot delete another user\'s bill', function () {
    $bill = Bill::factory()->create();

    Livewire::test('bills.list')->call('delete', $bill->id)->assertNotFound();

    expect($bill->fresh())->not->toBeNull();
});

it('never shows another user\'s bills', function () {
    Bill::factory()->create(['name' => 'Someone else']);

    Livewire::test('bills.list')->set('showing', 'all')->assertDontSee('Someone else');
});

it('refreshes when bills change elsewhere', function () {
    $component = Livewire::test('bills.list')->assertDontSee('Late addition');

    Bill::factory()->for($this->user)->create(['name' => 'Late addition']);

    $component->dispatch('bills-changed')->assertSee('Late addition');
});

it('escapes bill names', function () {
    Bill::factory()->for($this->user)->create(['name' => '<script>alert(1)</script>']);

    Livewire::test('bills.list')->assertDontSeeHtml('<script>alert(1)</script>')->assertSee('<script>alert(1)</script>');
});

it('uses the user\'s timezone for the current month', function () {
    $this->travelTo(now()->setDate(2026, 12, 1)->setTime(3, 0)); // Still November 30 in Chicago.
    $this->user->update(['timezone' => 'America/Chicago']);
    Bill::factory()->for($this->user)->create(['name' => 'November only', 'months' => [11]]);

    Livewire::test('bills.list')->assertSee('For November 2026')->assertSee('November only');
});
