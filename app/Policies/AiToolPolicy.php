<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;

class AiToolPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, AiTool $aiTool): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, AiTool $aiTool): bool
    {
        return (bool) $user;
    }

    public function delete(User $user, AiTool $aiTool): bool
    {
        return (bool) $user;
    }
}

