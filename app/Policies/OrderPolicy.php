<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Commerce\Models\Order;
use App\Domains\Auth\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }

    public function update(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}

