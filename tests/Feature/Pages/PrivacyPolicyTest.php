<?php

use App\Models\User;

it('shows the privacy policy to guests and users', function (bool $signedIn) {
    if ($signedIn) {
        $this->actingAs(User::factory()->create());
    }

    $this->get('/privacy-policy')
        ->assertOk()
        ->assertSee('<title>Privacy Policy · '.config('app.name').'</title>', false)
        ->assertSee('portfolio application that is not intended for public use')
        ->assertSee('Cloudflare Turnstile')
        ->assertSee('9. About this application')
        ->assertDontSee('support@');
})->with([false, true]);

it('is linked from the footer of every layout', function () {
    $this->get('/login')->assertSee(route('privacy-policy'));

    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertSee(route('privacy-policy'));
});

it('links the favicons and web manifest, which exist', function () {
    $this->get('/login')
        ->assertSee('href="/favicon.ico"', false)
        ->assertSee('href="/apple-touch-icon.png"', false)
        ->assertSee('href="/site.webmanifest"', false);

    $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true);

    expect($manifest['name'])->toBe('Billify')
        ->and(collect($manifest['icons'])->pluck('src')->every(fn ($src) => is_file(public_path($src))))->toBeTrue()
        ->and(public_path('apple-touch-icon.png'))->toBeFile()
        ->and(filesize(public_path('favicon.ico')))->toBeGreaterThan(0);
});
