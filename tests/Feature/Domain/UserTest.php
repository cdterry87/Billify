<?php

use App\Enums\PayFrequency;
use App\Models\Bill;
use App\Models\User;
use Carbon\CarbonImmutable;

it('has sensible defaults and never mass assigns the demo flag', function () {
    $user = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'secret-password', 'is_demo' => true]);

    expect($user->fresh())
        ->income_cents->toBe(0)
        ->pay_frequency->toBe(PayFrequency::Monthly)
        ->timezone->toBe('UTC')
        ->is_demo->toBeFalse();
});

it('reports the current time in the user timezone', function () {
    CarbonImmutable::setTestNow('2026-12-01 03:00:00 UTC');

    $user = User::factory()->make(['timezone' => 'America/Chicago']);

    expect($user->now()->toDateTimeString())->toBe('2026-11-30 21:00:00')
        ->and($user->now()->month)->toBe(11);
});

it('deletes its bills with it', function () {
    $bill = Bill::factory()->create();

    $bill->user->delete();

    $this->assertModelMissing($bill);
});
