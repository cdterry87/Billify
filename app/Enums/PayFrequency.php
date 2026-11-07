<?php

namespace App\Enums;

/**
 * How often the user is paid. The value is the number of pay periods per year.
 */
enum PayFrequency: int
{
    case Yearly = 1;
    case Monthly = 12;
    case BiWeekly = 26;
    case Weekly = 52;

    public function label(): string
    {
        return match ($this) {
            self::Yearly => 'Yearly (annual salary)',
            self::Monthly => 'Monthly',
            self::BiWeekly => 'Bi-weekly (every two weeks)',
            self::Weekly => 'Weekly',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
