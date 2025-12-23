<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Blog\Models\Post;
use App\Domains\Auth\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Post $post): bool
    {
        return (bool) $user;
    }

    public function delete(User $user, Post $post): bool
    {
        return (bool) $user;
    }
}

