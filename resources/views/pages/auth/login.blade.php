<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest'), Title('Log in')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

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

    <form class="stack" wire:submit="login" novalidate>
        <x-ui.field name="email" label="Email" type="email" autocomplete="email" required autofocus wire:model="email" />
        <x-ui.field name="password" label="Password" type="password" autocomplete="current-password" required wire:model="password" />

        <div class="auth__row">
            <x-ui.checkbox name="remember" label="Remember me" wire:model="remember" />
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        <x-ui.button type="submit" block wire:loading.attr="disabled">Log in</x-ui.button>
    </form>

    <p class="auth__footer">
        New to {{ config('app.name') }}? <a href="{{ route('register') }}">Create an account</a>
    </p>
</x-ui.card>
