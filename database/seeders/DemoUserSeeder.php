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
        // Delete any existing demo user
        User::where('demo', true)->delete();

        // Create demo user
        $demoUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'income' => '100000',
            'frequency' => '1',
            'password' => Hash::make('password1'),
            'demo' => true
        ]);

        // Create bills for demo user
        Bill::factory(10)->create([
            'user_id' => $demoUser->id
        ]);
    }
}
