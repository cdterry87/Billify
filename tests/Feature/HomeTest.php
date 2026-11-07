<?php

it('renders the home page', function () {
    $this->get('/')->assertOk()->assertSee(config('app.name'));
});

it('passes the health check', function () {
    $this->get('/up')->assertOk();
});
