<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create my user
        User::factory()->create([
            'name' => 'Chase Terry',
            'email' => 'chase@chaseterry.com',
            'income' => '100000',
            'frequency' => '1',
            'password' => Hash::make('password1'),
            'demo' => false
        ]);

        // Run the demo user seeder
        $this->call(DemoUserSeeder::class);
    }
}
