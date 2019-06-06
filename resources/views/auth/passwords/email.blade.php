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
                    Billify &copy; {{ date('Y') }}
                </div>
            </form>
        </div>
    </div>
@endsection