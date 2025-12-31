<?php

declare(strict_types=1);

namespace App\Domains\Courses\Policies;

use App\Domains\Courses\Models\Course;
use App\Domains\Auth\Models\User;
use App\Enums\CourseStatus;
use App\Enums\UserRole;

/**
 * Course Policy
 * Authorization rules for Courses
 */
class CoursePolicy
{
    /**
     * Determine whether anyone can view the listing
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether anyone can view the course
     */
    public function view(?User $user, Course $course): bool
    {
        // Published courses are public
        if ($course->status === CourseStatus::Published) {
            return true;
        }

        // Draft/Archived courses only visible to admins and teacher
        if ($user === null) {
            return false;
        }

        return $user->role === UserRole::Admin || $user->id === $course->teacher_id;
    }

    /**
     * Determine whether the user can create courses
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Teacher]);
    }

    /**
     * Determine whether the user can update the course
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->id === $course->teacher_id;
    }

    /**
     * Determine whether the user can delete the course
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can enroll in the course
     */
    public function enroll(User $user, Course $course): bool
    {
        // Must be published
        if ($course->status !== CourseStatus::Published) {
            return false;
        }

        // Can't enroll if already enrolled
        return !$user->isEnrolled($course);
    }

    /**
     * Determine whether the user can access course content
     */
    public function accessContent(User $user, Course $course): bool
    {
        // Enrolled users can access
        if ($user->isEnrolled($course)) {
            return true;
        }

        // Teacher and admin can always access
        return $user->role === UserRole::Admin || $user->id === $course->teacher_id;
    }
}
