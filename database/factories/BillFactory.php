<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random category option key
        $categoryOptions = Bill::getCategoryOptions();
        $category = array_rand($categoryOptions);

        // Set all months to false by default, which makes them all recur monthly
        $january = false;
        $february = false;
        $march = false;
        $april = false;
        $may = false;
        $june = false;
        $july = false;
        $august = false;
        $september = false;
        $october = false;
        $november = false;
        $december = false;

        // Algorithm to sometimes set certain months the bill is due
        $randomNumber = $this->faker->numberBetween(1, 12);
        if ($randomNumber >= 10) {
            if ($randomNumber == 10) {
                $april = true;
                $october = true;
            } elseif ($randomNumber == 11) {
                $may = true;
                $november = true;
            } else {
                $june = true;
                $december = true;
            }
        }

        return [
            'user_id' => User::factory(),
            'name' => ucwords($this->faker->words($this->faker->numberBetween(2, 5), true)),
            'category' => $category,
            'description' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'day' => $this->faker->numberBetween(1, 28),
            'january' => $january,
            'february' => $february,
            'march' => $march,
            'april' => $april,
            'may' => $may,
            'june' => $june,
            'july' => $july,
            'august' => $august,
            'september' => $september,
            'october' => $october,
            'november' => $november,
            'december' => $december,
        ];
    }
}
