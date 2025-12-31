<?php

declare(strict_types=1);

namespace App\Domains\Courses\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\CourseLicense;
use App\Domains\Commerce\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Action to assign a license to a user for a course
 */
class AssignLicenseAction
{
    public function __construct(
        private readonly GenerateLicenseKeyAction $generateLicenseKeyAction
    ) {
    }

    /**
     * Assign a license to a user for a course
     *
     * @param User $user The user to assign the license to
     * @param Course $course The course to assign the license for
     * @param User|null $assignedBy The admin user assigning the license (nullable)
     * @param Order|null $order The order associated with this license (nullable)
     * @param string|null $licenseKey Custom license key (if null, will be generated)
     * @param \DateTimeInterface|null $expiresAt Expiration date (nullable for lifetime access)
     * @param string|null $notes Additional notes
     * @return CourseLicense The created license
     */
    public function handle(
        User $user,
        Course $course,
        ?User $assignedBy = null,
        ?Order $order = null,
        ?string $licenseKey = null,
        ?\DateTimeInterface $expiresAt = null,
        ?string $notes = null
    ): CourseLicense {
        return DB::transaction(function () use ($user, $course, $assignedBy, $order, $licenseKey, $expiresAt, $notes) {
            // Generate license key if not provided
            if (!$licenseKey) {
                $licenseKey = $this->generateLicenseKeyAction->handle();
                
                // Ensure uniqueness
                while (CourseLicense::where('license_key', $licenseKey)->exists()) {
                    $licenseKey = $this->generateLicenseKeyAction->handle();
                }
            } else {
                // Check if provided license key is unique
                if (CourseLicense::where('license_key', $licenseKey)->exists()) {
                    throw new \Exception('License key already exists');
                }
            }

            // Create the license
            return CourseLicense::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $order?->id,
                'license_key' => $licenseKey,
                'assigned_by' => $assignedBy?->id,
                'expires_at' => $expiresAt,
                'is_active' => true,
                'notes' => $notes,
            ]);
        });
    }
}

