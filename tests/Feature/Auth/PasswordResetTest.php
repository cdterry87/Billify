<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

const NEUTRAL_MESSAGE = 'If an account exists for that email, a reset link is on its way.';

it('shows the forgot password page', function () {
    $this->get('/forgot-password')->assertOk()->assertSee('Send reset link');
});

it('sends a reset link to an existing user', function () {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendLink')
        ->assertHasNoErrors()
        ->assertSee(NEUTRAL_MESSAGE);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $url = $notification->toMail($user)->actionUrl;

        return str_starts_with($url, url('/reset-password/'.$notification->token))
            && str_contains($url, 'email='.urlencode($user->email));
    });
});

it('shows the same message for an unknown email', function () {
    Notification::fake();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'nobody@example.com')
        ->call('sendLink')
        ->assertHasNoErrors()
        ->assertSee(NEUTRAL_MESSAGE);

    Notification::assertNothingSent();
});

it('shows the same message when the broker throttles a repeat request', function () {
    Notification::fake();
    $user = User::factory()->create();

    foreach (range(1, 2) as $attempt) {
        Livewire::test('pages::auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendLink')
            ->assertHasNoErrors()
            ->assertSee(NEUTRAL_MESSAGE);
    }

    Notification::assertSentToTimes($user, ResetPassword::class, 1);
});

it('validates the forgot password email', function () {
    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'not-an-email')
        ->call('sendLink')
        ->assertHasErrors('email')
        ->assertDontSee(NEUTRAL_MESSAGE);
});

it('limits forgot password requests per IP', function () {
    Notification::fake();

    foreach (range(1, 5) as $attempt) {
        Livewire::test('pages::auth.forgot-password')->set('email', "user{$attempt}@example.com")->call('sendLink');
    }

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'user6@example.com')
        ->call('sendLink')
        ->assertHasErrors('email');
});

it('prefills the email on the reset page', function () {
    $this->get('/reset-password/some-token?email=grace%40example.com')
        ->assertOk()
        ->assertSee('Choose a new password');

    Livewire::withQueryParams(['email' => 'grace@example.com'])
        ->test('pages::auth.reset-password', ['token' => 'some-token'])
        ->assertSet('email', 'grace@example.com')
        ->assertSet('token', 'some-token');
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create(['password' => 'old-password', 'remember_token' => 'old']);
    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue()
        ->and($user->remember_token)->not->toBe('old')
        ->and(session('toast.type'))->toBe('success');

    $this->assertGuest();
});

it('rejects an invalid token', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    Livewire::test('pages::auth.reset-password', ['token' => 'wrong'])
        ->set('email', $user->email)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertHasErrors(['email' => __(Password::INVALID_TOKEN)])
        ->assertSet('password', '');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('rejects an expired token', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    $token = Password::createToken($user);

    $this->travel(config('auth.passwords.users.expire') + 1)->minutes();

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertHasErrors('email');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('keeps the token locked against tampering', function () {
    Livewire::test('pages::auth.reset-password', ['token' => 'original'])
        ->set('token', 'tampered');
})->throws(CannotUpdateLockedPropertyException::class);

it('validates the new password', function (array $input, string $field) {
    Livewire::test('pages::auth.reset-password', ['token' => 'token'])
        ->set(['email' => 'grace@example.com', 'password' => 'new-password', 'password_confirmation' => 'new-password', ...$input])
        ->call('resetPassword')
        ->assertHasErrors($field);
})->with([
    'invalid email' => [['email' => 'grace'], 'email'],
    'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'different'], 'password'],
]);

it('limits reset attempts per IP', function () {
    foreach (range(1, 5) as $attempt) {
        Livewire::test('pages::auth.reset-password', ['token' => 'wrong'])
            ->set(['email' => 'grace@example.com', 'password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->call('resetPassword');
    }

    Livewire::test('pages::auth.reset-password', ['token' => 'wrong'])
        ->set(['email' => 'grace@example.com', 'password' => 'new-password', 'password_confirmation' => 'new-password'])
        ->call('resetPassword')
        ->assertSeeText('Too many attempts');
});

it('schedules expired reset tokens to be cleared hourly', function () {
    $this->artisan('schedule:list')->expectsOutputToContain('auth:clear-resets');
});
