<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Blog\Models\Post;
use App\Domains\Commerce\Models\Order;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Interactions\Comment;
use App\Interactions\Review;

// Domain-based policies
use App\Domains\AiTools\Policies\AiToolPolicy;
use App\Domains\Blog\Policies\PostPolicy;
use App\Domains\Commerce\Policies\OrderPolicy;
use App\Domains\Courses\Policies\CoursePolicy;
use App\Domains\Courses\Policies\EnrollmentPolicy;
use App\Interactions\Policies\CommentPolicy;
use App\Interactions\Policies\ReviewPolicy;

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
        // Domain Policies
        AiTool::class => AiToolPolicy::class,
        Post::class => PostPolicy::class,
        Order::class => OrderPolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        
        // Interactions Policies
        Comment::class => CommentPolicy::class,
        Review::class => ReviewPolicy::class,
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

