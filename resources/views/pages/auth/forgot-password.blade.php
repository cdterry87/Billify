<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest'), Title('Forgot password')] class extends Component
{
    public string $email = '';

    public bool $sent = false;

    public function sendLink(): void
    {
        $this->validate(['email' => ['required', 'string', 'email', 'max:255']]);

        $key = 'forgot-password|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        RateLimiter::hit($key, 60);

        // The result is ignored on purpose: the page never reveals whether an account exists.
        // The demo account's password can't be reset, so it never gets a link.
        if (! User::query()->where('email', $this->email)->where('is_demo', true)->exists()) {
            Password::sendResetLink(['email' => $this->email]);
        }

        $this->sent = true;
    }
};
?>

<x-ui.card class="auth">
    <header class="auth__header">
        <h1 class="auth__title">Reset your password</h1>
        <p class="auth__lead">Enter your email and we'll send you a reset link.</p>
    </header>

    @if ($sent)
        <p class="notice" role="status">
            If an account exists for that email, a reset link is on its way. The link expires in {{ config('auth.passwords.users.expire') }} minutes.
        </p>
    @endif

    <form class="stack" wire:submit="sendLink" novalidate>
        <x-ui.field name="email" label="Email" type="email" autocomplete="email" required autofocus wire:model="email" />
        <x-ui.button type="submit" block wire:loading.attr="disabled">Send reset link</x-ui.button>
    </form>

    <p class="auth__footer">
        Remembered it? <a href="{{ route('login') }}">Back to log in</a>
    </p>
</x-ui.card>
