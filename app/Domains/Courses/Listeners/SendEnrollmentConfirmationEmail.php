<?php

declare(strict_types=1);

namespace App\Domains\Courses\Listeners;

use App\Domains\Courses\Events\CourseEnrolled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Send enrollment confirmation email when user enrolls in a course
 */
class SendEnrollmentConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CourseEnrolled $event): void
    {
        // TODO: Implement email sending logic
        // Mail::to($event->enrollment->user)->send(new EnrollmentConfirmationMail($event->enrollment));
    }
}

