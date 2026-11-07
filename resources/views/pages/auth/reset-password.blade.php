<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest'), Title('Choose a new password')] class extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
        ]);

        $key = 'reset-password|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        RateLimiter::hit($key, 60);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->reset('password', 'password_confirmation');

            throw ValidationException::withMessages(['email' => __($status)]);
        }

        session()->flash('toast', ['type' => 'success', 'message' => 'Your password has been reset. Log in with your new password.']);

        $this->redirectRoute('login');
    }
};
?>

<x-ui.card class="auth">
    <header class="auth__header">
        <h1 class="auth__title">Choose a new password</h1>
        <p class="auth__lead">Use at least 8 characters.</p>
    </header>

    <form class="stack" wire:submit="resetPassword" novalidate>
        <x-ui.field name="email" label="Email" type="email" autocomplete="email" required wire:model="email" />
        <x-ui.field name="password" label="New password" type="password" autocomplete="new-password" required autofocus wire:model="password" />
        <x-ui.field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required wire:model="password_confirmation" />
        <x-ui.button type="submit" block wire:loading.attr="disabled">Reset password</x-ui.button>
    </form>

    <p class="auth__footer">
        <a href="{{ route('login') }}">Back to log in</a>
    </p>
</x-ui.card>
