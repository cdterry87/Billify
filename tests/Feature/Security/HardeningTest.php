<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Providers\AppServiceProvider;
use App\Rules\Turnstile;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

afterEach(fn () => TrustProxies::flushState());

it('sends hardening headers on every response', function (string $url) {
    $response = $this->get($url);

    expect($response->headers->get('Content-Security-Policy'))->toContain("frame-ancestors 'none'")
        ->and($response->headers->get('X-Frame-Options'))->toBe('DENY')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin')
        ->and($response->headers->has('Permissions-Policy'))->toBeTrue()
        ->and($response->headers->has('X-Powered-By'))->toBeFalse()
        ->and($response->headers->has('Strict-Transport-Security'))->toBeFalse();
})->with(['login' => '/login', 'privacy policy' => '/privacy-policy', 'not found' => '/no-such-page']);

it('asks browsers to stay on HTTPS once a request arrives over it', function () {
    $this->get('https://localhost/login')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

it('signs out other sessions once the password changes', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->actingAs($user)->get('/')->assertOk();

    // Another device changes the password; this session still holds the old hash.
    $user->forceFill(['password' => 'new-password'])->save();

    $this->get('/')->assertRedirect('/login');
    $this->assertGuest();
});

it('encrypts the queued reset email, which holds the plain token', function () {
    expect(new ResetPasswordNotification('token'))->toBeInstanceOf(ShouldBeEncrypted::class);
});

it('refuses Cloudflare test keys for Turnstile in production', function () {
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);
    app()->detectEnvironment(fn () => 'production');

    $validator = Validator::make(['turnstile' => 'token'], ['turnstile' => new Turnstile]);

    expect($validator->fails())->toBeTrue();
    Http::assertNothingSent();
});

it('fails the Turnstile check while no secret key is set', function () {
    Http::fake();
    config(['billify.turnstile.secret_key' => null]);

    expect(Validator::make(['turnstile' => 'token'], ['turnstile' => new Turnstile])->fails())->toBeTrue();
    Http::assertNothingSent();
});

it('reads the client IP from trusted proxies only', function (string $proxies) {
    config(['billify.trusted_proxies' => $proxies]);
    (new AppServiceProvider(app()))->boot();
    Route::get('/_test/ip', fn () => request()->ip());

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertSee('203.0.113.9');

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertSee($proxies === '*' ? '203.0.113.9' : '198.51.100.7');
})->with(['a range' => '10.0.0.0/8, 192.168.0.1', 'everything' => '*']);

it('ignores forwarded headers when no proxy is trusted', function () {
    Route::get('/_test/ip', fn () => request()->ip());

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertSee('198.51.100.7');
});

it('builds HTTPS links when the app URL uses HTTPS', function () {
    config(['app.url' => 'https://billify.example']);
    (new AppServiceProvider(app()))->boot();

    expect(route('login'))->toStartWith('https://');
});
