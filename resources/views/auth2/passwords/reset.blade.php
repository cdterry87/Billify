@extends('layouts.app')

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

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-field">
                    <input type="email" id="email" name="email" maxlength="250" value="{{ old('email') }}" required>
                    <label for="email">Email</label>
                </div>

                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>

                <div class="input-field">
                    <input id="password-confirm" type="password" name="password_confirmation" required>
                    <label for="password">Confirm Password</label>
                </div>

                <div class="center-align">
                    <button type="submit" class="btn deep-purple">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
