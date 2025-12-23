<?php

declare(strict_types=1);

namespace App\Domains\Auth\Events;

use App\Domains\Auth\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a new user registers
 */
class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user
    ) {
    }
}

