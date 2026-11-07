<?php

it('passes the health check', function () {
    $this->get('/up')->assertOk();
});
