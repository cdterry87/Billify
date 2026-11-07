<?php

namespace App\Console\Commands;

use App\Support\DemoAccount;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

#[Signature('demo:reset')]
#[Description('Create the demo account, or restore its details and sample bills')]
class DemoResetCommand extends Command
{
    public function handle(): int
    {
        try {
            $user = DemoAccount::reset();
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info("The demo account {$user->email} has been reset with ".count(DemoAccount::BILLS).' sample bills.');

        return self::SUCCESS;
    }
}
