<h4>Hi, {{ $email }}.</h4>

<p>
    We received a request to change the password for your account.
    Please click the link below to reset your password. This link will
    expire in 24 hours.
</p>

<a href="{{ route('reset-password', ['token' => $token]) }}">Reset Password</a>

<p>
    If you did not initiate a request, please ignore this email.
    For your security, please do not share this email with anyone.
</p>

<div>
    Thank you,
    {{ config('app.name') }}
</div>
