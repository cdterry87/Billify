<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_routine_cleanup_job()
    {
        // Create a demo user with client, project, and issue
        $demoUser = User::factory()->create(['demo' => true]);
        $demoUserBill = Bill::factory()->create(['user_id' => $demoUser->id]);

        // Create another non-demo user with client, project, and issue
        $nonDemoUser = User::factory()->create(['demo' => false]);
        $nonDemoUserBill = Bill::factory()->create(['user_id' => $nonDemoUser->id]);

        // Assert database has bills for demo and non-demo users
        $this->assertDatabaseHas('bills', ['id' => $demoUserBill->id]);
        $this->assertDatabaseHas('bills', ['id' => $nonDemoUserBill->id]);

        // Run the job
        $job = new \App\Jobs\RoutineCleanupJob();
        $job();

        // Assert database does not have client, project, and issue
        $this->assertDatabaseMissing('bills', ['id' => $demoUserBill->id]);

        // Assert database still has client, project, and issue for non-demo user
        $this->assertDatabaseHas('bills', ['id' => $nonDemoUserBill->id]);
    }

    public function test_password_reset_cleanup_job()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a non-expired password reset token
        $passwordResetToken = PasswordResetToken::create([
            'email' => $user1->email,
            'token' => Str::uuid(),
            'created_at' => now()->subMinutes(30),
        ]);

        // Create an expired password reset token that's 25 hours
        $expiredPasswordResetToken = PasswordResetToken::create([
            'email' => $user2->email,
            'token' => Str::uuid(),
            'created_at' => now()->subHours(25),
        ]);

        // Run the job
        $job = new \App\Jobs\PasswordResetCleanupJob();
        $job();

        // Assert database still has non-expired password reset token
        $this->assertDatabaseHas('password_reset_tokens', ['token' => $passwordResetToken->token]);

        // Assert database does not have expired password reset token
        $this->assertDatabaseMissing('password_reset_tokens', ['token' => $expiredPasswordResetToken->token]);
    }
}
