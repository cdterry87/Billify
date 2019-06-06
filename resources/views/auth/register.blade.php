@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-content">
            <h4 class="center-align">Billify</h4>
            <h6 class="center-align">Manage Your Finances</h6>

            <hr/>

            <div class="row">
                @include('layouts.errors')
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="input-field">
                        <input type="text" id="name" name="name" maxlength="250" value="{{ old('name') }}" required>
                        <label for="name">Name</label>
                    </div>

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

                <br/>

                <div class="center-align">
                    <button type="submit" class="btn deep-purple">
                        {{ __('Register') }}
                    </button>
                </div>

                <br>

                <div class="center-align">
                    Already have an account?
                    <a href="{{ route('login') }}" class="blue-text">
                        {{ __('Log in') }}
                    </a>
                </div>

                <br>

                <hr/>

                <div class="center-align">
                    Billify &copy; {{ date('Y') }}
                </div>
            </form>
        </div>
    </div>
@endsection