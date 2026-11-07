<?php

namespace App\Models;

use App\Enums\BillCategory;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\BillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

/**
 * A recurring bill. When `months` is null the bill recurs every month;
 * otherwise it applies only in the listed months (1 to 12).
 */
#[Fillable(['name', 'category', 'description', 'amount_cents', 'due_day', 'months'])]
class Bill extends Model
{
    /** @use HasFactory<BillFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'category' => BillCategory::class,
            'amount_cents' => 'integer',
            'due_day' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bills that apply in the given month (1 to 12).
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function dueInMonth(Builder $query, int $month): void
    {
        $query->where(fn (Builder $query) => $query
            ->whereNull('months')
            ->orWhereJsonContains('months', $month));
    }

    public function appliesInMonth(int $month): bool
    {
        return $this->months === null || in_array($month, $this->months, true);
    }

    /**
     * The bill's due date in the given month, clamped to the month's last day
     * so that a bill due on the 31st falls on the 28th or 29th in February.
     */
    public function dueDateIn(CarbonInterface $month): CarbonImmutable
    {
        $start = $month->toImmutable()->startOfMonth();

        return $start->setDay(min($this->due_day, $start->daysInMonth));
    }

    /**
     * @return Attribute<list<int>|null, list<int>|null>
     */
    protected function months(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : json_decode($value, true),
            set: function (?array $value) {
                $months = self::normalizeMonths($value);

                return $months === null ? null : json_encode($months);
            },
        );
    }

    /**
     * Sort and de-duplicate months. No months and all twelve both mean "every month", stored as null.
     *
     * @param  array<int|string>|null  $months
     * @return list<int>|null
     */
    public static function normalizeMonths(?array $months): ?array
    {
        $months = collect($months ?? [])
            ->map(fn (int|string $month) => (int) $month)
            ->unique()
            ->sort()
            ->values();

        if ($months->contains(fn (int $month) => $month < 1 || $month > 12)) {
            throw new InvalidArgumentException('Months must be between 1 and 12.');
        }

        return $months->isEmpty() || $months->count() === 12 ? null : $months->all();
    }
}
