<?php

namespace App\Support;

use App\Enums\BillCategory;
use App\Enums\PayFrequency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Creates the demo account, or restores it to its original state.
 */
final class DemoAccount
{
    /**
     * $100,000 a year, after taxes.
     */
    public const INCOME_CENTS = 10_000_000;

    /**
     * @var list<array{name: string, category: BillCategory, description: string|null, amount_cents: int, due_day: int, months: list<int>|null}>
     */
    public const BILLS = [
        ['name' => 'Rent', 'category' => BillCategory::Housing, 'description' => 'Apartment rent', 'amount_cents' => 165_000, 'due_day' => 1, 'months' => null],
        ['name' => 'Electric', 'category' => BillCategory::Utilities, 'description' => null, 'amount_cents' => 9_500, 'due_day' => 12, 'months' => null],
        ['name' => 'Internet', 'category' => BillCategory::Utilities, 'description' => 'Fiber, 1 Gbps', 'amount_cents' => 6_000, 'due_day' => 18, 'months' => null],
        ['name' => 'Groceries', 'category' => BillCategory::Food, 'description' => 'Monthly budget', 'amount_cents' => 60_000, 'due_day' => 5, 'months' => null],
        ['name' => 'Car payment', 'category' => BillCategory::Transportation, 'description' => null, 'amount_cents' => 38_500, 'due_day' => 15, 'months' => null],
        ['name' => 'Car insurance', 'category' => BillCategory::Insurance, 'description' => 'Paid twice a year', 'amount_cents' => 72_000, 'due_day' => 20, 'months' => [1, 7]],
        ['name' => 'Student loan', 'category' => BillCategory::Debt, 'description' => null, 'amount_cents' => 31_000, 'due_day' => 25, 'months' => null],
        ['name' => 'Retirement', 'category' => BillCategory::Retirement, 'description' => 'IRA contribution', 'amount_cents' => 50_000, 'due_day' => 28, 'months' => null],
        ['name' => 'Streaming', 'category' => BillCategory::Entertainment, 'description' => 'Video and music', 'amount_cents' => 2_800, 'due_day' => 9, 'months' => null],
        ['name' => 'Holiday gifts', 'category' => BillCategory::GiftsDonations, 'description' => null, 'amount_cents' => 40_000, 'due_day' => 10, 'months' => [11, 12]],
    ];

    /**
     * Whether the demo account's credentials are set.
     */
    public static function configured(): bool
    {
        return filled(config('billify.demo.email')) && filled(config('billify.demo.password'));
    }

    /**
     * Create the demo account, or restore its details and sample bills.
     *
     * @throws RuntimeException when the credentials are missing, or a regular account already uses the email.
     */
    public static function reset(): User
    {
        if (! self::configured()) {
            throw new RuntimeException('Set DEMO_EMAIL and DEMO_PASSWORD to create the demo account.');
        }

        $config = config('billify.demo');

        return DB::transaction(function () use ($config) {
            $user = User::query()->where('is_demo', true)->first();

            if ($user === null && User::query()->where('email', $config['email'])->exists()) {
                throw new RuntimeException("A regular account already uses {$config['email']}, so it can't become the demo account.");
            }

            $user ??= new User;

            $user->forceFill([
                'name' => $config['name'],
                'email' => $config['email'],
                'password' => $config['password'],
                'income_cents' => self::INCOME_CENTS,
                'pay_frequency' => PayFrequency::Yearly,
                'timezone' => $config['timezone'],
                'is_demo' => true,
                'email_verified_at' => now(),
                // Ends "remember me" logins from before the reset.
                'remember_token' => Str::random(60),
            ])->save();

            $user->bills()->delete();
            $user->bills()->createMany(self::BILLS);

            return $user;
        });
    }
}
