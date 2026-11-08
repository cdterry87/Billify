<?php

use App\Console\Commands\CreateUserCommand;
use App\Enums\PayFrequency;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an account from the answers to its questions', function () {
    config(['billify.demo_mode' => true]);

    $this->artisan('user:create')
        ->expectsQuestion('Name', 'Chase')
        ->expectsQuestion('Email', 'chase@example.com')
        ->expectsQuestion('Password', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-strong-password')
        ->expectsQuestion('Income after taxes, per pay period', '5000.50')
        ->expectsChoice('Paid', 26, PayFrequency::options())
        ->expectsQuestion('Timezone', 'America/Chicago')
        ->expectsOutputToContain('Created chase@example.com.')
        ->assertSuccessful();

    $user = User::sole();

    expect($user)
        ->name->toBe('Chase')
        ->email->toBe('chase@example.com')
        ->income_cents->toBe(500_050)
        ->pay_frequency->toBe(PayFrequency::BiWeekly)
        ->timezone->toBe('America/Chicago')
        ->is_demo->toBeFalse()
        ->and(Hash::check('a-strong-password', $user->password))->toBeTrue();
});

it('takes everything but the password from options', function () {
    $this->artisan('user:create', [
        '--name' => 'Chase',
        '--email' => 'chase@example.com',
        '--income' => '100000',
        '--frequency' => '1',
        '--timezone' => 'UTC',
    ])
        ->expectsQuestion('Password', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-strong-password')
        ->assertSuccessful();

    expect(User::sole())
        ->income_cents->toBe(10_000_000)
        ->pay_frequency->toBe(PayFrequency::Yearly);
});

it('refuses invalid options', function (array $options, string $error) {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->artisan('user:create', [
        '--name' => 'Chase',
        '--email' => 'chase@example.com',
        '--income' => '100',
        '--frequency' => '12',
        '--timezone' => 'UTC',
        ...$options,
    ])
        ->expectsQuestion('Password', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-strong-password')
        ->expectsOutputToContain($error)
        ->assertFailed();

    expect(User::count())->toBe(1);
})->with([
    'email in use' => [['--email' => 'taken@example.com'], 'The email has already been taken.'],
    'demo email' => [['--email' => 'demo@example.test'], 'That email is reserved for the demo account.'],
    'bad email' => [['--email' => 'not-an-email'], 'The email field must be a valid email address.'],
    'bad income' => [['--income' => '1.005'], 'The income must be a plain number'],
    'bad frequency' => [['--frequency' => '7'], 'The selected frequency is invalid.'],
    'bad timezone' => [['--timezone' => 'Mars/Base'], 'The timezone field must be a valid timezone.'],
]);

it('refuses a password that does not match its confirmation', function () {
    $this->artisan('user:create', ['--name' => 'Chase', '--email' => 'chase@example.com', '--income' => '0', '--frequency' => '12', '--timezone' => 'UTC'])
        ->expectsQuestion('Password', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-different-password')
        ->expectsOutputToContain('The password field confirmation does not match.')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('checks each answer as it is typed', function () {
    $command = new CreateUserCommand;
    $check = (fn (string $field) => $this->check($field))->call($command, 'password');

    expect($check('short'))->toBe('The password field must be at least 8 characters.')
        ->and($check('long-enough-password'))->toBeNull();
});

it('explains that it needs a terminal to ask for the password', function () {
    $this->artisan('user:create', ['--no-interaction' => true])
        ->expectsOutputToContain('Run this in an interactive terminal')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('uses UTC when the timezone is left blank', function () {
    $this->artisan('user:create', ['--name' => 'Chase', '--email' => 'chase@example.com', '--income' => '0', '--frequency' => '12'])
        ->expectsQuestion('Password', 'a-strong-password')
        ->expectsQuestion('Confirm password', 'a-strong-password')
        ->expectsQuestion('Timezone', '')
        ->assertSuccessful();

    expect(User::sole()->timezone)->toBe('UTC');
});
