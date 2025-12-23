<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Blog\Models\Post;
use App\Domains\Commerce\Models\Order;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Policies\AiToolPolicy;
use App\Policies\PostPolicy;
use App\Policies\OrderPolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AiTool::class => AiToolPolicy::class,
        Post::class => PostPolicy::class,
        Order::class => OrderPolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin gate - check if user has admin role
        Gate::define('admin', function ($user) {
            return $user?->isAdmin() ?? false;
        });

        // Admin override for all abilities
        Gate::before(function ($user, $ability) {
            // Check by role first
            if ($user?->isAdmin()) {
                return true;
            }

            // Fallback: Simple admin override via env email (for backward compatibility)
            $adminEmail = config('app.admin_email');
            if ($adminEmail && $user?->email === $adminEmail) {
                return true;
            }

            return null;
        });
    }
}

