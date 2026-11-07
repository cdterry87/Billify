<?php

use App\Models\Bill;
use App\Models\User;
use App\Support\UpcomingBills;
use Carbon\CarbonImmutable;

function upcoming(User $user, string $today): array
{
    return UpcomingBills::for($user, CarbonImmutable::parse($today))
        ->map(fn ($item) => [$item->bill->name, $item->dueDate->toDateString(), $item->daysUntil])
        ->all();
}

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('includes bills due from today through five days ahead', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Today', 'due_day' => 10]);
    Bill::factory()->for($this->user)->create(['name' => 'Edge', 'due_day' => 15]);
    Bill::factory()->for($this->user)->create(['name' => 'Too late', 'due_day' => 16]);
    Bill::factory()->for($this->user)->create(['name' => 'Passed', 'due_day' => 9]);

    expect(upcoming($this->user, '2026-06-10'))->toBe([
        ['Today', '2026-06-10', 0],
        ['Edge', '2026-06-15', 5],
    ]);
});

it('spans the end of the year', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Rent', 'due_day' => 1]);
    Bill::factory()->for($this->user)->create(['name' => 'Phone', 'due_day' => 30]);

    expect(upcoming($this->user, '2026-12-28'))->toBe([
        ['Phone', '2026-12-30', 2],
        ['Rent', '2027-01-01', 4],
    ]);
});

it('spans February into March in common and leap years', function (string $today, array $expected) {
    Bill::factory()->for($this->user)->create(['name' => 'End of month', 'due_day' => 31]);
    Bill::factory()->for($this->user)->create(['name' => 'Second', 'due_day' => 2]);

    expect(upcoming($this->user, $today))->toBe($expected);
})->with([
    'common year' => ['2026-02-26', [['End of month', '2026-02-28', 2], ['Second', '2026-03-02', 4]]],
    'leap year' => ['2028-02-26', [['End of month', '2028-02-29', 3], ['Second', '2028-03-02', 5]]],
]);

it('honours the months a bill applies in', function () {
    Bill::factory()->for($this->user)->inMonths([1])->create(['name' => 'January only', 'due_day' => 2]);
    Bill::factory()->for($this->user)->inMonths([12])->create(['name' => 'December only', 'due_day' => 2]);
    Bill::factory()->for($this->user)->inMonths([6])->create(['name' => 'June only', 'due_day' => 29]);

    expect(upcoming($this->user, '2026-12-29'))->toBe([['January only', '2027-01-02', 4]]);
});

it('orders by due date, then by amount, largest first', function () {
    Bill::factory()->for($this->user)->create(['name' => 'Small', 'due_day' => 12, 'amount_cents' => 1_000]);
    Bill::factory()->for($this->user)->create(['name' => 'Large', 'due_day' => 12, 'amount_cents' => 90_000]);
    Bill::factory()->for($this->user)->create(['name' => 'First', 'due_day' => 11, 'amount_cents' => 500]);

    expect(array_column(upcoming($this->user, '2026-06-10'), 0))->toBe(['First', 'Large', 'Small']);
});

it('ignores other users bills', function () {
    Bill::factory()->create(['due_day' => 10]);

    expect(upcoming($this->user, '2026-06-10'))->toBe([]);
});

it('uses today in the user timezone by default', function () {
    CarbonImmutable::setTestNow('2026-12-01 03:00:00 UTC');
    $this->user->update(['timezone' => 'America/Chicago']);
    Bill::factory()->for($this->user)->create(['name' => 'Rent', 'due_day' => 30]);

    expect(UpcomingBills::for($this->user)->map(fn ($item) => $item->dueDate->toDateString())->all())->toBe(['2026-11-30']);
});
