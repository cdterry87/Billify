<?php

namespace Database\Seeders;

use App\Support\DemoAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed a local database. Accounts come only from environment variables, never from fixed values,
     * and production is left alone: it gets its demo account from the scheduled `demo:reset`.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        if (! DemoAccount::configured()) {
            $this->command?->warn('Set DEMO_EMAIL and DEMO_PASSWORD to seed the demo account.');

            return;
        }

        $user = DemoAccount::reset();

        $this->command?->info("Seeded the demo account {$user->email}.");
    }
}
