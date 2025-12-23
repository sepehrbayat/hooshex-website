<?php

declare(strict_types=1);

namespace App\Domains\Courses\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Events\CourseEnrolled;
use App\Domains\Courses\Exceptions\AlreadyEnrolledException;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Action to enroll a user in a course
 * Following Single Responsibility Principle - one action, one purpose
 */
class EnrollUserAction
{
    /**
     * Enroll a user in a course
     *
     * @param User $user The user to enroll
     * @param Course $course The course to enroll in
     * @return Enrollment The created enrollment
     * @throws AlreadyEnrolledException If user is already enrolled
     */
    public function handle(User $user, Course $course): Enrollment
    {
        // 1. Validation Logic
        if ($user->isEnrolled($course)) {
            throw new AlreadyEnrolledException();
        }

        // 2. Database Logic
        $enrollment = $course->enrollments()->create([
            'user_id' => $user->id,
            'enrolled_at' => now(),
        ]);

        // 3. Event Firing
        CourseEnrolled::dispatch($enrollment);

        return $enrollment;
    }
}

