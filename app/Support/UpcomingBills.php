<?php

namespace App\Support;

use App\Models\Bill;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Bills falling due from today through the next few days, across month and year ends,
 * honouring each bill's months. Dates are in the user's timezone.
 */
final class UpcomingBills
{
    public const int WINDOW_DAYS = 5;

    /**
     * @return Collection<int, UpcomingBill>
     */
    public static function for(User $user, ?CarbonImmutable $today = null, int $days = self::WINDOW_DAYS): Collection
    {
        $today = ($today ?? $user->now())->startOfDay();
        $end = $today->addDays($days);

        // The window is short, so it touches at most two calendar months.
        $months = collect([$today->startOfMonth(), $end->startOfMonth()])
            ->unique(fn (CarbonImmutable $month) => $month->format('Y-m'));

        return $user->bills()->get()
            ->flatMap(fn (Bill $bill) => $months
                ->filter(fn (CarbonImmutable $month) => $bill->appliesInMonth($month->month))
                ->map(fn (CarbonImmutable $month) => $bill->dueDateIn($month))
                ->filter(fn (CarbonImmutable $date) => $date->betweenIncluded($today, $end))
                ->map(fn (CarbonImmutable $date) => new UpcomingBill($bill, $date, (int) $today->diffInDays($date))))
            ->sort(fn (UpcomingBill $a, UpcomingBill $b) => [$a->dueDate, $b->bill->amount_cents] <=> [$b->dueDate, $a->bill->amount_cents])
            ->values();
    }
}
