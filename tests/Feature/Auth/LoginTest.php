<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('shows the login page to guests', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('<title>Log in · '.config('app.name').'</title>', false)
        ->assertSee('Forgot password?')
        ->assertSee(route('register'));
});

it('sends guests to the login page from the dashboard', function () {
    $this->get('/')->assertRedirect(route('login'));
});

it('sends signed-in users away from guest pages', function (string $path) {
    $this->actingAs(User::factory()->create())
        ->get($path)
        ->assertRedirect(route('dashboard'));
})->with(['/login', '/register', '/forgot-password', '/reset-password/token']);

it('logs a user in and sends them to the dashboard', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('returns users to the page they asked for', function () {
    $user = User::factory()->create(['password' => 'secret-password']);
    session()->put('url.intended', url('/somewhere'));

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->call('login')
        ->assertRedirect(url('/somewhere'));
});

it('remembers the user when asked', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->set('remember', true)
        ->call('login');

    expect($user->fresh()->remember_token)->not->toBeNull();
});

it('rejects a wrong password with a generic message', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.failed')])
        ->assertSet('password', '');

    $this->assertGuest();
});

it('validates the login form', function (array $input, string $field) {
    Livewire::test('pages::auth.login')
        ->set($input)
        ->call('login')
        ->assertHasErrors($field);
})->with([
    'missing email' => [['email' => '', 'password' => 'x'], 'email'],
    'invalid email' => [['email' => 'not-an-email', 'password' => 'x'], 'email'],
    'missing password' => [['email' => 'a@example.com', 'password' => ''], 'password'],
]);

it('locks out after five failed attempts per email and IP', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    foreach (range(1, 5) as $attempt) {
        Livewire::test('pages::auth.login')
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login');
    }

    Livewire::test('pages::auth.login')
        ->set('email', strtoupper($user->email))
        ->set('password', 'secret-password')
        ->call('login')
        ->assertHasErrors('email')
        ->assertNoRedirect();

    $this->assertGuest();
});

it('limits one address to twenty failed attempts across all accounts', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    foreach (range(1, 20) as $attempt) {
        Livewire::test('pages::auth.login')
            ->set('email', "user{$attempt}@example.com")
            ->set('password', 'common-password')
            ->call('login');
    }

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->call('login')
        ->assertHasErrors('email')
        ->assertSet('password', '');

    $this->assertGuest();
});

it('never sends the password back after logging in', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->call('login')
        ->assertRedirect(route('dashboard'))
        ->assertSet('password', '');
});

it('clears failed attempts after a successful login', function () {
    $user = User::factory()->create(['password' => 'secret-password']);
    $key = 'login|'.$user->email.'|127.0.0.1';

    RateLimiter::hit($key);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'secret-password')
        ->call('login');

    expect(RateLimiter::attempts($key))->toBe(0);
});
