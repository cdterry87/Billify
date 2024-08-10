<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set demo user's email
        $demoUserEmail = 'demo@example.com';

        // Check if there is already a demo user
        $demoUser = User::where('email', $demoUserEmail)->first();

        // Create demo user if one doesn't already exist
        if (!$demoUser) {
            $demoUser = User::factory()->create([
                'name' => 'Demo User',
                'email' => $demoUserEmail,
                'income' => '100000',
                'frequency' => '1',
                'password' => Hash::make('password1'),
                'demo' => true
            ]);
        }

        // Check if demo user has any bills
        $demoUserBills = Bill::where('user_id', $demoUser->id)->count();

        // If demo user doesn't have any bills, add some
        if (!$demoUserBills) {
            Bill::factory(10)->create([
                'user_id' => $demoUser->id
            ]);
        }
    }
}
