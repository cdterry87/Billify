<?php

use App\Enums\BillCategory;
use App\Enums\PayFrequency;
use App\Models\Bill;
use App\Models\User;
use App\Support\DemoAccount;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Hash;

it('creates the demo account with its sample bills', function () {
    $this->artisan('demo:reset')
        ->expectsOutputToContain('The demo account demo@example.test has been reset with 10 sample bills.')
        ->assertSuccessful();

    $user = User::sole();

    expect($user)
        ->name->toBe('Demo User')
        ->email->toBe('demo@example.test')
        ->is_demo->toBeTrue()
        ->income_cents->toBe(10_000_000)
        ->pay_frequency->toBe(PayFrequency::Yearly)
        ->timezone->toBe('UTC')
        ->email_verified_at->not->toBeNull()
        ->and(Hash::check('demo-password', $user->password))->toBeTrue()
        ->and($user->bills()->count())->toBe(10)
        ->and($user->bills()->where('name', 'Car insurance')->sole()->months)->toBe([1, 7]);
});

it('restores everything the demo user changed', function () {
    $user = DemoAccount::reset();
    $user->forceFill([
        'name' => 'Changed',
        'income_cents' => 1,
        'pay_frequency' => PayFrequency::Weekly,
        'timezone' => 'Asia/Tokyo',
        'password' => 'something-else',
        'remember_token' => 'old-token',
    ])->save();
    $user->bills()->first()->delete();
    $user->bills()->first()->update(['amount_cents' => 1]);
    Bill::factory()->for($user)->create(['name' => 'Added by a visitor', 'category' => BillCategory::Other]);

    $this->artisan('demo:reset')->assertSuccessful();

    $user->refresh();

    expect(User::count())->toBe(1)
        ->and($user)
        ->name->toBe('Demo User')
        ->income_cents->toBe(10_000_000)
        ->pay_frequency->toBe(PayFrequency::Yearly)
        ->timezone->toBe('UTC')
        ->remember_token->not->toBe('old-token')
        ->and(Hash::check('demo-password', $user->password))->toBeTrue()
        ->and($user->bills()->pluck('name')->sort()->values()->all())
        ->toBe(collect(DemoAccount::BILLS)->pluck('name')->sort()->values()->all())
        ->and((int) $user->bills()->sum('amount_cents'))->toBe(collect(DemoAccount::BILLS)->sum('amount_cents'));
});

it('moves the demo account to new credentials', function () {
    DemoAccount::reset();
    config(['billify.demo.email' => 'new-demo@example.test', 'billify.demo.timezone' => 'America/Chicago']);

    $this->artisan('demo:reset')->assertSuccessful();

    expect(User::sole())
        ->email->toBe('new-demo@example.test')
        ->timezone->toBe('America/Chicago');
});

it('leaves other users alone', function () {
    $other = User::factory()->create();
    Bill::factory()->for($other)->count(2)->create();

    $this->artisan('demo:reset')->assertSuccessful();

    expect($other->fresh()->is_demo)->toBeFalse()
        ->and($other->bills()->count())->toBe(2);
});

it('fails without credentials', function (string $key) {
    config(["billify.demo.{$key}" => '']);

    $this->artisan('demo:reset')
        ->expectsOutputToContain('Set DEMO_EMAIL and DEMO_PASSWORD')
        ->assertFailed();

    expect(User::count())->toBe(0);
})->with(['email', 'password']);

it('never turns a regular account into the demo account', function () {
    $user = User::factory()->create(['email' => 'demo@example.test']);

    $this->artisan('demo:reset')
        ->expectsOutputToContain('A regular account already uses demo@example.test')
        ->assertFailed();

    expect($user->fresh()->is_demo)->toBeFalse();
});

it('is scheduled daily only in demo mode', function (bool $demoMode) {
    config(['billify.demo_mode' => $demoMode]);

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command, 'demo:reset'));

    expect($event->expression)->toBe('0 0 * * *')
        ->and($event->filtersPass(app()))->toBe($demoMode);
})->with([true, false]);

describe('the database seeder', function () {
    it('seeds the demo account', function () {
        $this->seed();

        expect(User::sole()->is_demo)->toBeTrue();
    });

    it('seeds nothing without credentials', function () {
        config(['billify.demo.email' => null]);

        $this->seed();

        expect(User::count())->toBe(0);
    });

    it('seeds nothing in production', function () {
        app()->detectEnvironment(fn () => 'production');

        $this->seed();

        expect(User::count())->toBe(0);
    });
});
