<?php

use App\Enums\BillCategory;
use App\Enums\PayFrequency;

it('keeps the sixteen bill categories with readable labels', function () {
    expect(BillCategory::cases())->toHaveCount(16)
        ->and(BillCategory::MedicalHealth->label())->toBe('Medical/Health')
        ->and(BillCategory::GiftsDonations->label())->toBe('Gifts/Donations')
        ->and(BillCategory::Housing->label())->toBe('Housing')
        ->and(BillCategory::options())->toHaveKey('medical_health', 'Medical/Health');
});

it('stores pay frequency as periods per year', function () {
    expect(array_map(fn (PayFrequency $f) => $f->value, PayFrequency::cases()))->toBe([1, 12, 26, 52])
        ->and(PayFrequency::options())->toBe([
            1 => 'Yearly (annual salary)',
            12 => 'Monthly',
            26 => 'Bi-weekly (every two weeks)',
            52 => 'Weekly',
        ]);
});
