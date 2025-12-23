<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Courses\Models\Course;
use App\Domains\Auth\Models\User;

class CoursePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Course $course): bool
    {
        return (bool) $user;
    }

    public function delete(User $user, Course $course): bool
    {
        return (bool) $user;
    }
}

