<?php

use App\Enums\PayFrequency;
use App\Models\Bill;
use App\Models\User;
use App\Support\IncomeSummary;
use Carbon\CarbonImmutable;

it('breaks income down by period for each frequency', function (PayFrequency $frequency, int $income, array $expected) {
    $user = User::factory()->create(['income_cents' => $income, 'pay_frequency' => $frequency]);

    $summary = IncomeSummary::for($user);

    expect([$summary->annualCents, $summary->monthlyCents, $summary->biWeeklyCents, $summary->weeklyCents])->toBe($expected);
})->with([
    'yearly' => [PayFrequency::Yearly, 10_000_000, [10_000_000, 833_333, 384_615, 192_308]],
    'monthly' => [PayFrequency::Monthly, 500_000, [6_000_000, 500_000, 230_769, 115_385]],
    'bi-weekly' => [PayFrequency::BiWeekly, 250_000, [6_500_000, 541_667, 250_000, 125_000]],
    'weekly' => [PayFrequency::Weekly, 100_000, [5_200_000, 433_333, 200_000, 100_000]],
]);

it('compares income with only the bills that apply in the month', function () {
    $user = User::factory()->create(['income_cents' => 500_000, 'pay_frequency' => PayFrequency::Monthly]);
    Bill::factory()->for($user)->create(['amount_cents' => 150_000]);
    Bill::factory()->for($user)->inMonths([6])->create(['amount_cents' => 50_000]);
    Bill::factory()->create(['amount_cents' => 999_999]);

    $june = IncomeSummary::for($user, CarbonImmutable::parse('2026-06-15'));
    $july = IncomeSummary::for($user, CarbonImmutable::parse('2026-07-15'));

    expect($june->monthlyBillsCents)->toBe(200_000)
        ->and($june->remainderCents())->toBe(300_000)
        ->and($june->billsVsIncomePercent())->toBe(40.0)
        ->and($july->monthlyBillsCents)->toBe(150_000)
        ->and($july->billsVsIncomePercent())->toBe(30.0);
});

it('uses the current month in the user timezone by default', function () {
    CarbonImmutable::setTestNow('2026-12-01 03:00:00 UTC');
    $user = User::factory()->create(['timezone' => 'America/Chicago']);
    Bill::factory()->for($user)->inMonths([11])->create(['amount_cents' => 10_000]);

    expect(IncomeSummary::for($user)->monthlyBillsCents)->toBe(10_000);
});

it('handles zero income without dividing by zero', function () {
    $user = User::factory()->create(['income_cents' => 0]);
    Bill::factory()->for($user)->create(['amount_cents' => 10_000]);

    $summary = IncomeSummary::for($user);

    expect($summary->billsVsIncomePercent())->toBeNull()
        ->and($summary->remainderCents())->toBe(-10_000);
});
