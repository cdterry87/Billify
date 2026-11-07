<?php

namespace App\Support;

use Illuminate\Support\Number;
use InvalidArgumentException;

/**
 * Money is stored as integer cents everywhere. This is the only place that converts to and from it.
 */
final class Money
{
    /**
     * Convert user input such as "12", "12.5", or "12.50" to cents without floating-point error.
     */
    public static function toCents(string|int|float $amount): int
    {
        $value = trim((string) $amount);

        if (! preg_match('/^(-)?(\d+)(?:\.(\d{1,2}))?$/', $value, $matches)) {
            throw new InvalidArgumentException("Invalid money amount [{$value}].");
        }

        $cents = (int) $matches[2] * 100 + (int) str_pad($matches[3] ?? '', 2, '0');

        return $matches[1] === '-' ? -$cents : $cents;
    }

    /**
     * Cents as a plain decimal string for form inputs, such as "1234.50".
     */
    public static function toDecimal(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $cents = abs($cents);

        return sprintf('%s%d.%02d', $sign, intdiv($cents, 100), $cents % 100);
    }

    /**
     * Cents formatted for display, such as "$1,234.50".
     */
    public static function format(int $cents): string
    {
        return Number::currency($cents / 100, 'USD', 'en');
    }
}
