<?php

use App\Models\User;

it('logs the user out and invalidates the session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['marker' => 'kept'])
        ->post('/logout')
        ->assertRedirect(route('login'))
        ->assertSessionMissing('marker');

    $this->assertGuest();
});

it('only accepts logout as a POST from a signed-in user', function () {
    $this->post('/logout')->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->get('/logout')->assertMethodNotAllowed();
});

it('shows a log out button on the dashboard', function () {
    $user = User::factory()->create(['name' => 'Ada Lovelace']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Hello, Ada Lovelace')
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('aria-label="Log out"', false);
});
