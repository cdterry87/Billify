<?php

namespace App\Support;

use App\Enums\BillCategory;

final readonly class CategoryTotal
{
    public function __construct(
        public BillCategory $category,
        public int $totalCents,
    ) {}
}
