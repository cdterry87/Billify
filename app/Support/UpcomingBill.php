<?php

namespace App\Support;

use App\Models\Bill;
use Carbon\CarbonImmutable;

final readonly class UpcomingBill
{
    public function __construct(
        public Bill $bill,
        public CarbonImmutable $dueDate,
        public int $daysUntil,
    ) {}
}
