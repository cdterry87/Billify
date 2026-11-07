<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

it('renders a branded page for each error', function (int $status, string $title) {
    Route::get('/_test/error', fn () => abort($status));

    $this->get('/_test/error')
        ->assertStatus($status)
        ->assertSee("<title>{$title} · ".config('app.name').'</title>', false)
        ->assertSee('portfolio application that is not intended for public use')
        ->assertDontSee('support@');
})->with([
    [403, 'Access denied'],
    [404, 'Page not found'],
    [419, 'Page expired'],
    [500, 'Something went wrong'],
    [503, 'Down for maintenance'],
]);

it('shows the 404 page for unknown URLs', function () {
    $this->get('/no-such-page')
        ->assertNotFound()
        ->assertSee('Page not found')
        ->assertSee('Back to '.config('app.name'));
});

it('shows the 403 page when a policy denies access', function () {
    $this->actingAs(User::factory()->create());
    Route::middleware('web')->get('/_test/forbidden', fn () => abort(403, 'Internal detail'));

    $this->get('/_test/forbidden')->assertForbidden()->assertDontSee('Internal detail');
});

it('offers to try again during maintenance', function () {
    Route::get('/_test/maintenance', fn () => abort(503));

    $this->get('/_test/maintenance')->assertSee('Try again');
});
