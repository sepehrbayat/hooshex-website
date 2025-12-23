<?php

declare(strict_types=1);

namespace App\Domains\Courses\Events;

use App\Domains\Courses\Models\Enrollment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a user enrolls in a course
 */
class CourseEnrolled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Enrollment $enrollment
    ) {
    }
}

