<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Bill;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creating an admin user with associated bills
        factory(User::class)->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'income' => 2000
        ])->each(function ($admin) {
            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Mortgage',
                'amount' => '1000',
                'day' => '30',
                'user_id' => 1,
            ]));

            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Utilities',
                'amount' => '150',
                'day' => '25',
                'user_id' => 1,
            ]));

            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Vehicle Loan',
                'amount' => '350',
                'day' => '20',
                'user_id' => 1,
            ]));

            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Cell Phone',
                'amount' => '100',
                'day' => '15',
                'user_id' => 1,
            ]));

            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Cable/Internet',
                'amount' => '150',
                'day' => '10',
                'user_id' => 1,
            ]));

            $admin->bills()->save(factory(Bill::class)->create([
                'name' => 'Student Loan',
                'amount' => '250',
                'day' => '5',
                'user_id' => 1,
            ]));
        });
    }
}
