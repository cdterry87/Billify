<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo mode
    |--------------------------------------------------------------------------
    |
    | Prepares the application for the portfolio site: registration is
    | disabled and the login page offers a one-click demo login.
    | The demo account itself is created and restored by `demo:reset`.
    |
    */

    'demo_mode' => (bool) env('DEMO_MODE', false),

    'demo' => [
        'name' => env('DEMO_NAME', 'Demo User'),
        'email' => env('DEMO_EMAIL'),
        'password' => env('DEMO_PASSWORD'),
        'timezone' => env('DEMO_TIMEZONE', 'UTC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bill limit
    |--------------------------------------------------------------------------
    |
    | The most bills one account may hold. Far more than a household needs,
    | and it stops a script (or a bot in the demo) from filling the database.
    |
    */

    'max_bills' => 100,

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated proxy IPs or CIDR ranges whose X-Forwarded-* headers are
    | believed, or "*" when the app is only reachable through the proxy. Rate
    | limits key on the client IP, so set this behind a load balancer or CDN.
    | Leave it empty when nothing sits in front of the app.
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES'),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    |
    | Protects registration. Local and CI environments use Cloudflare's
    | published test keys, which always pass.
    | See https://developers.cloudflare.com/turnstile/troubleshooting/testing/
    |
    */

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    ],

];
