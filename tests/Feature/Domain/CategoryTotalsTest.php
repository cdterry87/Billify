<?php

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Models\User;
use App\Support\CategoryTotals;

it('returns the top categories for a month, largest first', function () {
    $user = User::factory()->create();
    $amounts = [
        'housing' => [150_000],
        'debt' => [40_000, 30_000],
        'food' => [60_000],
        'insurance' => [20_000],
        'utilities' => [15_000],
        'savings' => [5_000],
    ];

    foreach ($amounts as $category => $values) {
        foreach ($values as $amount) {
            Bill::factory()->for($user)->create(['category' => $category, 'amount_cents' => $amount]);
        }
    }

    Bill::factory()->for($user)->inMonths([1])->create(['category' => 'education', 'amount_cents' => 999_999]);
    Bill::factory()->create(['category' => 'clothing', 'amount_cents' => 999_999]);

    $totals = CategoryTotals::for($user, 6);

    expect($totals->map(fn ($total) => [$total->category, $total->totalCents])->all())->toBe([
        [BillCategory::Housing, 150_000],
        [BillCategory::Debt, 70_000],
        [BillCategory::Food, 60_000],
        [BillCategory::Insurance, 20_000],
        [BillCategory::Utilities, 15_000],
    ]);
});

it('returns nothing when no bills apply', function () {
    expect(CategoryTotals::for(User::factory()->create(), 6))->toBeEmpty();
});
