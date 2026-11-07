<?php

use App\Support\Money;

it('converts input to cents without floating-point error', function (string|int|float $input, int $cents) {
    expect(Money::toCents($input))->toBe($cents);
})->with([
    ['12', 1200],
    ['12.5', 1250],
    ['12.50', 1250],
    ['0.29', 29],
    ['0.07', 7],
    [' 9999999.99 ', 999999999],
    ['-3.10', -310],
    [42, 4200],
    [19.99, 1999],
]);

it('rejects malformed amounts', function (string $input) {
    Money::toCents($input);
})->with(['', 'abc', '1.234', '1,000', '.5', '1e3'])->throws(InvalidArgumentException::class);

it('formats cents for inputs and display', function () {
    expect(Money::toDecimal(123450))->toBe('1234.50')
        ->and(Money::toDecimal(7))->toBe('0.07')
        ->and(Money::toDecimal(-1050))->toBe('-10.50')
        ->and(Money::format(123456))->toBe('$1,234.56')
        ->and(Money::format(0))->toBe('$0.00')
        ->and(Money::format(-5))->toBe('-$0.05');
});
