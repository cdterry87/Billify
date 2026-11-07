<?php

namespace Database\Factories;

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => ucwords(fake()->words(fake()->numberBetween(1, 3), true)),
            'category' => fake()->randomElement(BillCategory::cases()),
            'description' => fake()->optional()->sentence(),
            'amount_cents' => fake()->numberBetween(25, 1500) * 100,
            'due_day' => fake()->numberBetween(1, 28),
            'months' => null,
        ];
    }

    /**
     * @param  list<int>  $months
     */
    public function inMonths(array $months): static
    {
        return $this->state(['months' => $months]);
    }
}
