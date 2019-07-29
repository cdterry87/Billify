@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-content">
            <nav class="deep-purple">
                <div class="nav-wrapper">
                    <a class="brand-logo">Billify</a>
                    <ul id="nav-mobile" class="right hide-on-med-and-down">
                        <li>Simplify Your Finances</li>
                    </ul>
                </div>
            </nav>

            <div class="row">
                @include('layouts.demo')
                @include('layouts.errors')
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-field">
                    <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
                    <label for="email">Email</label>
                </div>

                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>

                <div>
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>{{ __('Remember Me') }}</span>
                    </label>
                </div>

                <br/>

                <div class="center-align">
                    <button type="submit" class="btn deep-purple">
                        {{ __('Login') }}
                    </button>

                    <a class="btn light-green" href="{{ route('register') }}">
                        {{ __('Register') }}
                    </a>
                </div>

                <br>

                <div class="center-align">
                    <a class="blue-text" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>

                <br>

                <hr/>

                <div class="copyright center-align">
                    {{ config('app.name', 'Laravel') }} &copy; {{ date('Y') }}
                </div>
            </form>
        </div>
    </div>
@endsection
