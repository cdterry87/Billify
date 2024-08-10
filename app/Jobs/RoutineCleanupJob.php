<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class RoutineCleanupJob implements ShouldQueue
{
    use Queueable;

    public function __invoke()
    {
        // Get all demo user ids
        $demoUsers = User::query()
            ->select('id')
            ->where('demo', true)
            ->get()
            ->pluck('id')
            ->toArray();

        // Delete demo user's bills
        Bill::query()
            ->whereIn('user_id', $demoUsers)
            ->delete();

        // Run DemoUserSeeder to repopulate data
        $seeder = new DemoUserSeeder();
        $seeder->run();
    }
}
