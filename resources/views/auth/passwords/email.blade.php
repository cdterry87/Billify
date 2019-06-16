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
                @include('layouts.errors')
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-field">
                    <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
                    <label for="email">Email</label>
                </div>

                <br/>

                <div class="center-align">
                    <button type="submit" class="btn deep-purple">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>

                <br>

                <div class="center-align">
                    <a href="{{ route('login') }}" class="blue-text">
                        {{ __('Return to Login') }}
                    </a>
                </div>

                <br>

                <hr/>

                <div class="center-align">
                    {{ config('app.name', 'Laravel') }} &copy; {{ date('Y') }}
                </div>
            </form>
        </div>
    </div>
@endsection
