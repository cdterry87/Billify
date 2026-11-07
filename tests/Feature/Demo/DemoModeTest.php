<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Support\DemoAccount;
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

    it('shows the demo credentials on the login page', function () {
        $this->get('/login')
            ->assertSee('Try the demo')
            ->assertSee('demo@example.test')
            ->assertSee('demo-password')
            ->assertSee('The demo data resets daily.');
    });

    it('hides the credentials while they are not set', function () {
        config(['billify.demo.password' => null]);

        $this->get('/login')->assertDontSee('Try the demo');
    });

    it('logs in as the demo user with one click', function () {
        $user = DemoAccount::reset();

        Livewire::test('pages::auth.login')
            ->call('loginAsDemo')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
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
