<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PasswordResetCleanupJob implements ShouldQueue
{
    use Queueable;

    public function __invoke()
    {
        // Delete password reset tokens older than 24 hours
        \App\Models\PasswordResetToken::where('created_at', '<', now()->subHours(24))->delete();
    }
}
