<?php

use App\Support\DemoAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest'), Title('Log in')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    #[Computed]
    public function demo(): bool
    {
        return config('billify.demo_mode') && DemoAccount::configured();
    }

    public function loginAsDemo(): void
    {
        abort_unless($this->demo, 404);

        $this->email = config('billify.demo.email');
        $this->password = config('billify.demo.password');
        $this->remember = false;

        $this->login();
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            $this->reset('password');

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($key);
        session()->regenerate();

        $this->redirectIntended(route('dashboard'));
    }

    private function throttleKey(): string
    {
        return 'login|'.Str::transliterate(Str::lower($this->email)).'|'.request()->ip();
    }
};
?>

<x-ui.card class="auth">
    <header class="auth__header">
        <h1 class="auth__title">Welcome back</h1>
        <p class="auth__lead">Log in to manage your bills.</p>
    </header>

    @if ($this->demo)
        <section class="demo-notice" aria-labelledby="demo-notice-title">
            <h2 class="demo-notice__title" id="demo-notice-title">Try the demo</h2>
            <dl class="demo-notice__credentials">
                <dt>Email</dt>
                <dd><code>{{ config('billify.demo.email') }}</code></dd>
                <dt>Password</dt>
                <dd><code>{{ config('billify.demo.password') }}</code></dd>
            </dl>
            <p class="demo-notice__note">The demo data resets daily.</p>
            <x-ui.button variant="secondary" block wire:click="loginAsDemo" wire:loading.attr="disabled">Log in as the demo user</x-ui.button>
        </section>
    @endif

    <form class="stack" wire:submit="login" novalidate>
        <x-ui.field name="email" label="Email" type="email" autocomplete="email" required autofocus wire:model="email" />
        <x-ui.field name="password" label="Password" type="password" autocomplete="current-password" required wire:model="password" />

        <div class="auth__row">
            <x-ui.checkbox name="remember" label="Remember me" wire:model="remember" />
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        <x-ui.button type="submit" block wire:loading.attr="disabled">Log in</x-ui.button>
    </form>

    @unless (config('billify.demo_mode'))
        <p class="auth__footer">
            New to {{ config('app.name') }}? <a href="{{ route('register') }}">Create an account</a>
        </p>
    @endunless
</x-ui.card>
