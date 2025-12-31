<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Policies;

use App\Domains\Commerce\Models\Order;
use App\Domains\Auth\Models\User;
use App\Enums\UserRole;

/**
 * Order Policy
 * Authorization rules for Orders
 */
class OrderPolicy
{
    /**
     * Determine whether the user can view any orders
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all orders
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Users can view their own orders (handled in query)
        return true;
    }

    /**
     * Determine whether the user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Users can only view their own orders
        return $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create orders
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create orders
    }

    /**
     * Determine whether the user can update the order
     */
    public function update(User $user, Order $order): bool
    {
        // Only admin can update orders
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can delete the order
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can download invoice
     */
    public function downloadInvoice(User $user, Order $order): bool
    {
        // User can download their own invoices
        if ($order->user_id === $user->id) {
            return true;
        }

        return $user->role === UserRole::Admin;
    }
}
