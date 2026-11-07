<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo mode
    |--------------------------------------------------------------------------
    |
    | Prepares the application for the portfolio site: registration is
    | disabled and the login page shows the demo account's credentials.
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
