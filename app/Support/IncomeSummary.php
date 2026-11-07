<?php

namespace App\Support;

use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Income broken down by period, against the bills that apply in one month. All amounts in cents.
 */
final readonly class IncomeSummary
{
    public function __construct(
        public int $annualCents,
        public int $monthlyCents,
        public int $biWeeklyCents,
        public int $weeklyCents,
        public int $monthlyBillsCents,
    ) {}

    public static function for(User $user, ?CarbonInterface $month = null): self
    {
        $month ??= $user->now();
        $annual = $user->income_cents * $user->pay_frequency->value;

        return new self(
            annualCents: $annual,
            monthlyCents: (int) round($annual / 12),
            biWeeklyCents: (int) round($annual / 26),
            weeklyCents: (int) round($annual / 52),
            monthlyBillsCents: (int) $user->bills()->dueInMonth($month->month)->sum('amount_cents'),
        );
    }

    public function remainderCents(): int
    {
        return $this->monthlyCents - $this->monthlyBillsCents;
    }

    /**
     * Bills as a percentage of monthly income, or null when there is no income to compare against.
     */
    public function billsVsIncomePercent(): ?float
    {
        if ($this->monthlyCents === 0) {
            return null;
        }

        return round($this->monthlyBillsCents / $this->monthlyCents * 100, 2);
    }
}
