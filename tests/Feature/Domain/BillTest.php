<?php

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Models\User;
use Carbon\CarbonImmutable;

it('normalizes months so every month has one representation', function (?array $input, ?array $stored) {
    $bill = Bill::factory()->create(['months' => $input]);

    expect($bill->fresh()->months)->toBe($stored);
})->with([
    'none' => [null, null],
    'empty' => [[], null],
    'all twelve' => [range(1, 12), null],
    'unsorted with duplicates' => [[12, '6', 6, 1], [1, 6, 12]],
]);

it('rejects months outside 1 to 12', function () {
    Bill::normalizeMonths([0, 13]);
})->throws(InvalidArgumentException::class);

it('casts its attributes', function () {
    $bill = Bill::factory()->create(['category' => 'medical_health', 'amount_cents' => 1999]);

    expect($bill->fresh())
        ->category->toBe(BillCategory::MedicalHealth)
        ->amount_cents->toBe(1999)
        ->user->toBeInstanceOf(User::class);
});

it('scopes to bills that apply in a month', function () {
    $user = User::factory()->create();
    $everyMonth = Bill::factory()->for($user)->create();
    $juneAndDecember = Bill::factory()->for($user)->inMonths([6, 12])->create();

    expect($user->bills()->dueInMonth(6)->pluck('id')->all())->toEqualCanonicalizing([$everyMonth->id, $juneAndDecember->id])
        ->and($user->bills()->dueInMonth(11)->pluck('id')->all())->toBe([$everyMonth->id])
        ->and($juneAndDecember->appliesInMonth(12))->toBeTrue()
        ->and($juneAndDecember->appliesInMonth(11))->toBeFalse()
        ->and($everyMonth->appliesInMonth(2))->toBeTrue();
});

it('clamps the due date to the end of shorter months', function (int $day, string $month, string $expected) {
    $bill = Bill::factory()->make(['due_day' => $day]);

    expect($bill->dueDateIn(CarbonImmutable::parse($month))->toDateString())->toBe($expected);
})->with([
    [31, '2026-02-10', '2026-02-28'],
    [31, '2028-02-10', '2028-02-29'],
    [30, '2026-04-30', '2026-04-30'],
    [31, '2026-04-01', '2026-04-30'],
    [15, '2026-07-31', '2026-07-15'],
]);

it('keeps the due date in the timezone of the month it is given', function () {
    $bill = Bill::factory()->make(['due_day' => 3]);

    expect($bill->dueDateIn(CarbonImmutable::parse('2026-05-20', 'America/Chicago'))->timezoneName)->toBe('America/Chicago');
});
