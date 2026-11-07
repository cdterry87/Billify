<?php

use App\Models\Bill;
use App\Models\User;

it('allows only the owner to view, update, or delete a bill', function (string $ability) {
    $bill = Bill::factory()->create();

    expect($bill->user->can($ability, $bill))->toBeTrue()
        ->and(User::factory()->create()->can($ability, $bill))->toBeFalse();
})->with(['view', 'update', 'delete']);
