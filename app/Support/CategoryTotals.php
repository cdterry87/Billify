<?php

namespace App\Support;

use App\Enums\BillCategory;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * The categories with the highest bill totals in one month.
 */
final class CategoryTotals
{
    /**
     * @return Collection<int, CategoryTotal>
     */
    public static function for(User $user, int $month, int $limit = 5): Collection
    {
        return $user->bills()
            ->dueInMonth($month)
            ->toBase()
            ->selectRaw('category, sum(amount_cents) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->orderBy('category')
            ->limit($limit)
            ->get()
            ->map(fn (object $row) => new CategoryTotal(BillCategory::from($row->category), (int) $row->total));
    }
}
