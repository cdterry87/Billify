<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    public function view(User $user, Bill $bill): bool
    {
        return $bill->user_id === $user->id;
    }

    public function update(User $user, Bill $bill): bool
    {
        return $bill->user_id === $user->id;
    }

    public function delete(User $user, Bill $bill): bool
    {
        return $bill->user_id === $user->id;
    }
}
