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

            <h6>{{ __('Verify Your Email Address') }}</h6>

            <div>
                @if (session('resent'))
                    <article class="card-panel green lighten-1 white-text">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </article>
                @endif

                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
            </div>
        </div>
    </div>
@endsection
