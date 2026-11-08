<?php

use App\Rules\Turnstile;
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

    /** Turnstile token for the demo button, so only people (not bots) get the one-click login. */
    public string $turnstile = '';

    #[Computed]
    public function demo(): bool
    {
        return config('billify.demo_mode') && DemoAccount::configured();
    }

    public function loginAsDemo(): void
    {
        abort_unless($this->demo, 404);

        try {
            $this->validate(
                ['turnstile' => ['required', new Turnstile(request()->ip())]],
                ['turnstile.required' => 'Please wait for the security check to finish, then try again.'],
            );
        } finally {
            // Tokens are single-use, so every attempt needs a fresh one.
            $this->reset('turnstile');
            $this->dispatch('turnstile-reset');
        }

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

        // Five tries per account from one address, and twenty per address across all accounts,
        // which stops one machine from trying a common password against many emails.
        $key = $this->throttleKey();
        $ipKey = 'login-ip|'.request()->ip();

        foreach ([$key => 5, $ipKey => 20] as $limiterKey => $maxAttempts) {
            if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
                $this->reset('password');

                throw ValidationException::withMessages([
                    'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($limiterKey)]),
                ]);
            }
        }

        $credentials = ['email' => $this->email, 'password' => $this->password];

        // The password is never sent back to the browser in the component's state.
        $this->reset('password');

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60);
            RateLimiter::hit($ipKey, 60);

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
            <p class="demo-notice__note">NOTE: The demo data resets daily.</p>
            {{-- Stays hidden unless Cloudflare wants a click to prove the visitor is a person. --}}
            <div class="demo-notice__check" wire:ignore x-data="turnstile(@js(config('billify.turnstile.site_key')), { appearance: 'interaction-only', action: 'demo-login' })"></div>
            @error('turnstile')<p class="field__error" role="alert">{{ $message }}</p>@enderror
            <x-ui.button variant="secondary" block wire:click="loginAsDemo" wire:loading.attr="disabled">Click to log in as the demo user</x-ui.button>
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
