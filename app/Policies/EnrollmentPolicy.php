<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Courses\Models\Enrollment;
use App\Domains\Auth\Models\User;

class EnrollmentPolicy
{
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->user_id === $user->id;
    }
}

