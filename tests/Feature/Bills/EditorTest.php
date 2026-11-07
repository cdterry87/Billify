<?php

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Models\User;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function editor(): Testable
{
    return Livewire::test('bills.editor');
}

function validBill(array $overrides = []): array
{
    return [
        'form.name' => 'Rent',
        'form.category' => 'housing',
        'form.description' => 'Apartment',
        'form.amount' => '1250.50',
        'form.due_day' => 1,
        'form.months' => [],
        ...$overrides,
    ];
}

it('opens empty for a new bill', function () {
    editor()
        ->dispatch('edit-bill', id: null)
        ->assertDispatched('open-modal', name: 'bill-editor')
        ->assertSet('billId', null)
        ->assertSet('form.name', '')
        ->assertSet('form.due_day', 1)
        ->assertSee('Add bill');
});

it('creates a bill and stays open in edit mode', function () {
    $component = editor()
        ->set(validBill(['form.months' => ['6', '1']]))
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('saved', true)
        ->assertSee('Bill saved successfully!')
        ->assertSee('Edit bill')
        ->assertDispatched('bills-changed');

    $bill = $this->user->bills()->sole();

    expect($bill)
        ->name->toBe('Rent')
        ->category->toBe(BillCategory::Housing)
        ->description->toBe('Apartment')
        ->amount_cents->toBe(125050)
        ->due_day->toBe(1)
        ->months->toBe([1, 6]);

    $component->assertSet('billId', $bill->id)->assertSet('form.months', ['1', '6']);
});

it('stores all twelve months as every month', function () {
    editor()->set(validBill(['form.months' => array_map(strval(...), range(1, 12))]))->call('save')->assertSet('form.months', []);

    expect($this->user->bills()->sole()->months)->toBeNull();
});

it('stores an empty description as null', function () {
    editor()->set(validBill(['form.description' => '  ']))->call('save');

    expect($this->user->bills()->sole()->description)->toBeNull();
});

it('loads and updates an existing bill', function () {
    $bill = Bill::factory()->for($this->user)->create(['name' => 'Gas', 'amount_cents' => 4500, 'months' => [3, 4]]);

    editor()
        ->dispatch('edit-bill', id: $bill->id)
        ->assertDispatched('open-modal', name: 'bill-editor')
        ->assertSet('billId', $bill->id)
        ->assertSet('form.name', 'Gas')
        ->assertSet('form.amount', '45.00')
        ->assertSet('form.months', ['3', '4'])
        ->set('form.amount', '50')
        ->call('save')
        ->assertHasNoErrors();

    expect($bill->fresh()->amount_cents)->toBe(5000)
        ->and($this->user->bills()->count())->toBe(1);
});

it('clears the form for a new bill after editing', function () {
    $bill = Bill::factory()->for($this->user)->create();

    editor()
        ->dispatch('edit-bill', id: $bill->id)
        ->call('startNew')
        ->assertSet('billId', null)
        ->assertSet('form.name', '')
        ->set(validBill(['form.name' => 'Second']))
        ->call('save');

    expect($this->user->bills()->count())->toBe(2);
});

it('deletes the bill being edited', function () {
    $bill = Bill::factory()->for($this->user)->create(['name' => 'Gym']);

    editor()
        ->dispatch('edit-bill', id: $bill->id)
        ->call('delete')
        ->assertSet('billId', null)
        ->assertDispatched('close-modal', name: 'bill-editor')
        ->assertDispatched('bills-changed')
        ->assertDispatched('toast', type: 'success', message: '"Gym" was deleted.');

    expect(Bill::count())->toBe(0);
});

it('does nothing when deleting without a bill', function () {
    editor()->call('delete')->assertNotDispatched('bills-changed');
});

it('cannot open another user\'s bill', function () {
    $bill = Bill::factory()->create();

    editor()
        ->dispatch('edit-bill', id: $bill->id)
        ->assertNotFound()
        ->assertNotDispatched('open-modal');
});

it('does not recreate a bill deleted while it was open', function () {
    $bill = Bill::factory()->for($this->user)->create();

    $component = editor()->dispatch('edit-bill', id: $bill->id);
    $bill->delete();

    $component->set('form.name', 'Back again')->call('save')->assertNotFound();

    expect(Bill::count())->toBe(0);
});

it('cannot tamper with the bill ID', function () {
    $bill = Bill::factory()->create();

    editor()->set('billId', $bill->id);
})->throws(CannotUpdateLockedPropertyException::class);

it('validates the bill form', function (array $input, string $field) {
    editor()->set(validBill($input))->call('save')->assertHasErrors($field);

    expect(Bill::count())->toBe(0);
})->with([
    'missing name' => [['form.name' => ''], 'form.name'],
    'long name' => [['form.name' => str_repeat('a', 65)], 'form.name'],
    'missing category' => [['form.category' => ''], 'form.category'],
    'unknown category' => [['form.category' => 'yachts'], 'form.category'],
    'long description' => [['form.description' => str_repeat('a', 256)], 'form.description'],
    'missing amount' => [['form.amount' => ''], 'form.amount'],
    'zero amount' => [['form.amount' => '0'], 'form.amount'],
    'negative amount' => [['form.amount' => '-5'], 'form.amount'],
    'three decimals' => [['form.amount' => '1.005'], 'form.amount'],
    'exponent' => [['form.amount' => '1e3'], 'form.amount'],
    'amount too large' => [['form.amount' => '10000000'], 'form.amount'],
    'day zero' => [['form.due_day' => 0], 'form.due_day'],
    'day 32' => [['form.due_day' => 32], 'form.due_day'],
    'day not a number' => [['form.due_day' => 'first'], 'form.due_day'],
    'month 13' => [['form.months' => ['13']], 'form.months.0'],
    'month 0' => [['form.months' => ['0']], 'form.months.0'],
    'duplicate months' => [['form.months' => ['2', '2']], 'form.months.0'],
]);

it('accepts the boundary values', function () {
    editor()
        ->set(validBill([
            'form.name' => str_repeat('a', 64),
            'form.description' => str_repeat('a', 255),
            'form.amount' => '9999999.99',
            'form.due_day' => 31,
        ]))
        ->call('save')
        ->assertHasNoErrors();

    editor()->set(validBill(['form.amount' => '0.01', 'form.due_day' => 1]))->call('save')->assertHasNoErrors();

    expect(Bill::pluck('amount_cents')->all())->toEqualCanonicalizing([999_999_999, 1]);
});
