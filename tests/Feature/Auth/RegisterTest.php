<?php

use App\Enums\PayFrequency;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

function passTurnstile(): void
{
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);
}

function registration(array $overrides = []): Testable
{
    return Livewire::test('pages::auth.register')->set([
        'name' => 'Grace Hopper',
        'email' => 'grace@example.com',
        'password' => 'correct-horse',
        'password_confirmation' => 'correct-horse',
        'income' => '2500.50',
        'pay_frequency' => '26',
        'timezone' => 'America/Chicago',
        'turnstile' => 'token',
        ...$overrides,
    ]);
}

it('shows the registration page with the Turnstile widget', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('<title>Create an account · '.config('app.name').'</title>', false)
        ->assertSee("x-data=\"turnstile('".config('billify.turnstile.site_key')."')\"", false);
});

it('does not load the Turnstile script on other pages', function () {
    $this->get('/login')->assertDontSee('x-data="turnstile', false);
});

it('registers a user, logs them in, and keeps their timezone', function () {
    passTurnstile();

    registration()
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::sole();

    expect($user)
        ->name->toBe('Grace Hopper')
        ->email->toBe('grace@example.com')
        ->timezone->toBe('America/Chicago')
        ->income_cents->toBe(250050)
        ->pay_frequency->toBe(PayFrequency::BiWeekly)
        ->is_demo->toBeFalse()
        ->and(Hash::check('correct-horse', $user->password))->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

it('falls back to UTC for an unknown timezone', function (string $timezone) {
    passTurnstile();

    registration(['timezone' => $timezone])->call('register')->assertHasNoErrors();

    expect(User::sole()->timezone)->toBe('UTC');
})->with(['', 'Mars/Olympus_Mons']);

it('sends the token, secret, and IP to Cloudflare', function () {
    passTurnstile();

    registration(['turnstile' => 'abc123'])->call('register');

    Http::assertSent(fn (Request $request) => $request->url() === config('billify.turnstile.verify_url')
        && $request['secret'] === config('billify.turnstile.secret_key')
        && $request['response'] === 'abc123'
        && $request['remoteip'] === '127.0.0.1');
});

it('fails closed when Turnstile verification fails', function (Closure $response) {
    Http::fake(['challenges.cloudflare.com/*' => $response]);

    registration()
        ->call('register')
        ->assertHasErrors('turnstile')
        ->assertSet('turnstile', '')
        ->assertDispatched('turnstile-reset');

    expect(User::count())->toBe(0);
    $this->assertGuest();
})->with([
    'rejected token' => fn () => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']]),
    'server error' => fn () => Http::response('Bad gateway', 502),
    'unreachable' => fn () => throw new ConnectionException('timeout'),
]);

it('requires a Turnstile token without calling Cloudflare', function () {
    Http::fake();

    registration(['turnstile' => ''])->call('register')->assertHasErrors('turnstile');

    Http::assertNothingSent();
});

it('resets the widget and passwords when other fields fail', function () {
    passTurnstile();

    registration(['email' => 'not-an-email'])
        ->call('register')
        ->assertHasErrors('email')
        ->assertSet('password', '')
        ->assertSet('turnstile', '')
        ->assertDispatched('turnstile-reset');
});

it('validates the registration form', function (array $input, string $field) {
    passTurnstile();

    registration($input)->call('register')->assertHasErrors($field);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'long name' => [['name' => str_repeat('a', 256)], 'name'],
    'missing email' => [['email' => ''], 'email'],
    'invalid email' => [['email' => 'grace'], 'email'],
    'long email' => [['email' => str_repeat('a', 244).'@example.com'], 'email'],
    'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'different-horse'], 'password'],
    'missing income' => [['income' => ''], 'income'],
    'negative income' => [['income' => '-1'], 'income'],
    'three decimals' => [['income' => '10.555'], 'income'],
    'income too large' => [['income' => '1000000000'], 'income'],
    'unknown frequency' => [['pay_frequency' => '2'], 'pay_frequency'],
]);

it('accepts zero income and the largest income', function (string $income, int $cents) {
    passTurnstile();

    registration(['income' => $income])->call('register')->assertHasNoErrors();

    expect(User::sole()->income_cents)->toBe($cents);
})->with([
    ['0', 0],
    ['999999999.99', 99_999_999_999],
]);

it('rejects an email that is already registered', function () {
    passTurnstile();
    User::factory()->create(['email' => 'grace@example.com']);

    registration()->call('register')->assertHasErrors('email');
});

it('accepts an eight character password', function () {
    passTurnstile();

    registration(['password' => '12345678', 'password_confirmation' => '12345678'])
        ->call('register')
        ->assertHasNoErrors();
});

it('limits registration attempts per IP', function () {
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

    foreach (range(1, 5) as $attempt) {
        registration()->call('register');
    }

    passTurnstile();

    registration()->call('register')->assertHasErrors('email')->assertNoRedirect();

    expect(User::count())->toBe(0);
});
