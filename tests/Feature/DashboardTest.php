<?php

use App\Models\Bill;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 11, 28)->setTime(12, 0));
    $this->user = User::factory()->create(['name' => 'Ada', 'income_cents' => 10_000_000, 'pay_frequency' => 1]);
    $this->actingAs($this->user);
});

it('renders the dashboard with its parts', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<title>Dashboard · '.config('app.name').'</title>', false)
        ->assertSee('Hello, Ada')
        ->assertSee('My monthly bills')
        ->assertSee('Add bill');
});

it('shows the income summary tiles', function () {
    Bill::factory()->for($this->user)->create(['amount_cents' => 421_300, 'due_day' => 10]);

    Livewire::test('pages::dashboard')
        ->assertSeeInOrder([
            'Annual income', '$100,000.00',
            'Monthly income', '$8,333.33',
            'Bi-weekly income', '$3,846.15',
            'Weekly income', '$1,923.08',
            'Monthly remainder', '$4,120.33',
            'Bill-Income Ratio', '50.56%',
        ]);
});

it('shows N/A and empty charts with no income or bills', function () {
    $this->user->update(['income_cents' => 0]);

    Livewire::test('pages::dashboard')
        ->assertSee('N/A')
        ->assertSee('Add your income and bills to compare them.')
        ->assertSee('No bills are due this month.')
        ->assertDontSee('due soon');
});

it('charts income against bills and the top categories', function () {
    Bill::factory()->for($this->user)->create(['category' => 'housing', 'amount_cents' => 180_000, 'due_day' => 10]);
    Bill::factory()->for($this->user)->create(['category' => 'medical_health', 'amount_cents' => 22_000, 'due_day' => 10]);

    Livewire::test('pages::dashboard')
        ->assertSeeInOrder(['Income vs Bills', 'Income', 'Bills', 'Top bill categories', 'Housing', '$1,800.00', 'Medical/Health', '$220.00']);
});

it('lists bills due soon across the month end', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Today bill', 'due_day' => 28, 'amount_cents' => 1_000]);
    Bill::factory()->for($this->user)->create(['name' => 'Tomorrow bill', 'due_day' => 29]);
    Bill::factory()->for($this->user)->create(['name' => 'December bill', 'due_day' => 2, 'months' => [12]]);
    Bill::factory()->for($this->user)->create(['name' => 'Far bill', 'due_day' => 15]);

    Livewire::test('pages::dashboard')
        ->assertSee('You have bills due soon')
        ->assertSeeInOrder(['Today bill', 'due today', '$10.00', 'Tomorrow bill', 'due tomorrow', 'December bill', 'due in 4 days'])
        ->assertDontSee('Far bill due');
});

it('refreshes the summary when bills change', function () {
    $component = Livewire::test('pages::dashboard')->assertSee('$8,333.33');

    Bill::factory()->for($this->user)->create(['amount_cents' => 333_333, 'due_day' => 10]);

    $component->dispatch('bills-changed')->assertSee('$5,000.00');
});

it('flows from adding a bill to the dashboard totals', function () {
    Livewire::test('bills.editor')
        ->set(['form.name' => 'Rent', 'form.category' => 'housing', 'form.amount' => '1000', 'form.due_day' => 10])
        ->call('save')
        ->assertDispatched('bills-changed');

    Livewire::test('pages::dashboard')->assertSee('$7,333.33');
});

it('refreshes when the profile changes', function () {
    $component = Livewire::test('pages::dashboard')->assertSee('$8,333.33');

    $this->user->update(['income_cents' => 12_000_000]);

    $component->dispatch('profile-updated')->assertSee('$10,000.00');
});
