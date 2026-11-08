<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Support\DemoAccount;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

describe('with demo mode on', function () {
    beforeEach(fn () => config(['billify.demo_mode' => true]));

    it('hides registration', function () {
        $this->get('/register')->assertNotFound();

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Create an account')
            ->assertDontSee(route('register'));
    });

    it('refuses a registration form left open from before', function () {
        config(['billify.demo_mode' => false]);
        $component = Livewire::test('pages::auth.register');
        config(['billify.demo_mode' => true]);

        $component->call('register')->assertNotFound();
        expect(User::count())->toBe(0);
    });

    it('offers a demo login on the login page without showing the credentials', function () {
        $this->get('/login')
            ->assertSee('Try the demo')
            ->assertSee('Click to log in as the demo user')
            ->assertSee('The demo data resets daily.')
            ->assertDontSee('demo@example.test')
            ->assertDontSee('demo-password');
    });

    it('hides the demo login while the credentials are not set', function () {
        config(['billify.demo.password' => null]);

        $this->get('/login')->assertDontSee('Try the demo');
    });

    it('logs in as the demo user with one click', function () {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);
        $user = DemoAccount::reset();

        Livewire::test('pages::auth.login')
            ->set('turnstile', 'token')
            ->call('loginAsDemo')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        Http::assertSent(fn (Request $request) => $request['response'] === 'token');
    });

    it('guards the demo button with an invisible Turnstile check', function () {
        $this->get('/login')
            ->assertSee("turnstile('".config('billify.turnstile.site_key')."'", false)
            ->assertSee('interaction-only', false);
    });

    it('refuses the demo login without a Turnstile token', function () {
        Http::fake();
        DemoAccount::reset();

        Livewire::test('pages::auth.login')
            ->call('loginAsDemo')
            ->assertHasErrors(['turnstile' => 'Please wait for the security check to finish, then try again.'])
            ->assertNoRedirect();

        $this->assertGuest();
        Http::assertNothingSent();
    });

    it('refuses the demo login when Cloudflare rejects the token', function () {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);
        DemoAccount::reset();

        Livewire::test('pages::auth.login')
            ->set('turnstile', 'bot-token')
            ->call('loginAsDemo')
            ->assertHasErrors('turnstile')
            ->assertSet('turnstile', '')
            ->assertDispatched('turnstile-reset')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('needs no Turnstile token for a normal login', function () {
        Http::fake();
        $user = User::factory()->create(['password' => 'secret-password']);

        Livewire::test('pages::auth.login')
            ->set('email', $user->email)
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        Http::assertNothingSent();
    });
});

describe('with demo mode off', function () {
    it('allows registration', function () {
        $this->get('/register')->assertOk();
    });

    it('shows no demo information on the login page', function () {
        $this->get('/login')
            ->assertSee(route('register'))
            ->assertDontSee('Try the demo')
            ->assertDontSee('demo@example.test');
    });

    it('refuses the one-click demo login', function () {
        DemoAccount::reset();

        Livewire::test('pages::auth.login')->call('loginAsDemo')->assertNotFound();

        $this->assertGuest();
    });
});

it('never sends the demo user a password reset link', function () {
    Notification::fake();
    $user = DemoAccount::reset();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendLink')
        ->assertHasNoErrors()
        ->assertSee('If an account exists for that email, a reset link is on its way.');

    Notification::assertNothingSent();
    Notification::assertNotSentTo($user, ResetPasswordNotification::class);
});
