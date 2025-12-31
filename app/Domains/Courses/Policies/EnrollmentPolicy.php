<?php

declare(strict_types=1);

namespace App\Domains\Courses\Policies;

use App\Domains\Courses\Models\Enrollment;
use App\Domains\Auth\Models\User;
use App\Enums\UserRole;

/**
 * Enrollment Policy
 * Authorization rules for Course Enrollments
 */
class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any enrollments
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view the enrollment
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // User can view their own enrollment
        if ($enrollment->user_id === $user->id) {
            return true;
        }

        // Admin can view all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Teacher can view enrollments for their courses
        return $enrollment->course?->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the enrollment
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can access course content
     */
    public function accessContent(User $user, Enrollment $enrollment): bool
    {
        // Must be their own enrollment
        if ($enrollment->user_id !== $user->id) {
            return false;
        }

        // Check expiration
        if ($enrollment->expires_at && $enrollment->expires_at < now()) {
            return false;
        }

        return true;
    }
}
