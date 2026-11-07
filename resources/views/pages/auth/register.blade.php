<?php

use App\Enums\PayFrequency;
use App\Models\User;
use App\Support\Money;
use App\Rules\Turnstile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest'), Title('Create an account')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $income = '';

    public string $pay_frequency = '12';

    /** Detected in the browser; falls back to UTC when missing or invalid. */
    public string $timezone = '';

    public string $turnstile = '';

    public function register(): void
    {
        $key = 'register|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        RateLimiter::hit($key, 600);

        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed', Password::defaults()],
                'income' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0', 'max:999999999.99'],
                'pay_frequency' => ['required', Rule::enum(PayFrequency::class)],
                'turnstile' => ['required', new Turnstile(request()->ip())],
            ], [
                'turnstile.required' => 'Please complete the security check and try again.',
                'income.regex' => 'The income must be a plain number with at most 2 decimal places.',
            ]);
        } catch (ValidationException $exception) {
            // Turnstile tokens are single-use, so every failed attempt needs a fresh one.
            $this->reset('turnstile', 'password', 'password_confirmation');
            $this->dispatch('turnstile-reset');

            throw $exception;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'income_cents' => Money::toCents($validated['income']),
            'pay_frequency' => (int) $validated['pay_frequency'],
            'timezone' => in_array($this->timezone, timezone_identifiers_list(), true) ? $this->timezone : 'UTC',
        ]);

        event(new Registered($user));

        Auth::login($user);
        session()->regenerate();

        $this->redirectRoute('dashboard');
    }
};
?>

<x-ui.card class="auth">
    <header class="auth__header">
        <h1 class="auth__title">Create an account</h1>
        <p class="auth__lead">Track every bill against your income.</p>
    </header>

    <form class="stack" wire:submit="register" novalidate
        x-init="$wire.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone ?? ''">
        <x-ui.field name="name" label="Name" autocomplete="name" required autofocus wire:model="name" />
        <x-ui.field name="email" label="Email" type="email" autocomplete="email" required wire:model="email" />
        <div class="form-row">
            <x-ui.field name="income" label="Income after taxes ($)" inputmode="decimal" placeholder="0.00" hint="Per pay period." required wire:model="income" />
            <x-ui.field name="pay_frequency" label="Paid" as="select" :options="PayFrequency::options()" required wire:model="pay_frequency" />
        </div>
        <x-ui.field name="password" label="Password" type="password" autocomplete="new-password" hint="At least 8 characters." required wire:model="password" />
        <x-ui.field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required wire:model="password_confirmation" />

        <div class="turnstile">
            <div wire:ignore x-data="turnstile(@js(config('billify.turnstile.site_key')))"></div>
            @error('turnstile')<p class="field__error" role="alert">{{ $message }}</p>@enderror
        </div>

        <x-ui.button type="submit" block wire:loading.attr="disabled">Create account</x-ui.button>
    </form>

    <p class="auth__footer">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </p>
</x-ui.card>
