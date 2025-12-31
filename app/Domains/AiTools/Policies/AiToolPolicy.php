<?php

declare(strict_types=1);

namespace App\Domains\AiTools\Policies;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Auth\Models\User;
use App\Enums\UserRole;

/**
 * AI Tool Policy
 * Authorization rules for AI Tools
 */
class AiToolPolicy
{
    /**
     * Determine whether anyone can view the listing
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether anyone can view the AI tool
     */
    public function view(?User $user, AiTool $aiTool): bool
    {
        // Published tools are public
        if ($aiTool->published_at !== null && $aiTool->published_at <= now()) {
            return true;
        }

        // Unpublished tools only visible to admins
        return $user !== null && $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can create AI tools
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can update the AI tool
     */
    public function update(User $user, AiTool $aiTool): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can delete the AI tool
     */
    public function delete(User $user, AiTool $aiTool): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can bookmark the AI tool
     */
    public function bookmark(User $user, AiTool $aiTool): bool
    {
        return $aiTool->published_at !== null;
    }
}
