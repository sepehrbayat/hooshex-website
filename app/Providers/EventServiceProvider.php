<?php

declare(strict_types=1);

namespace App\Providers;

// Domain Events
use App\Domains\Auth\Events\UserRegistered;
use App\Domains\Auth\Listeners\SendWelcomeNotification;
use App\Domains\Commerce\Events\OrderCreated;
use App\Domains\Commerce\Events\OrderPaid;
use App\Domains\Commerce\Listeners\SendOrderConfirmationEmail;
use App\Domains\Commerce\Listeners\SendPaymentConfirmationEmail;
use App\Domains\Courses\Events\CourseEnrolled;
use App\Domains\Courses\Listeners\SendEnrollmentConfirmationEmail;
use App\Domains\Blog\Events\PostPublished;

// Interactions Events
use App\Interactions\Events\CommentCreated;
use App\Interactions\Events\CommentApproved;
use App\Interactions\Events\ReviewCreated;
use App\Interactions\Events\ReviewApproved;
use App\Interactions\Listeners\NotifyAdminNewComment;
use App\Interactions\Listeners\SendCommentApprovalEmail;
use App\Interactions\Listeners\UpdateModelRating;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Auth Domain Events
        UserRegistered::class => [
            SendWelcomeNotification::class,
        ],
        
        // Commerce Domain Events
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
        ],
        OrderPaid::class => [
            SendPaymentConfirmationEmail::class,
        ],
        
        // Courses Domain Events
        CourseEnrolled::class => [
            SendEnrollmentConfirmationEmail::class,
        ],
        
        // Blog Domain Events
        PostPublished::class => [
            // Add listeners here when needed (e.g., notify subscribers)
        ],
        
        // Interactions Events
        CommentCreated::class => [
            NotifyAdminNewComment::class,
        ],
        CommentApproved::class => [
            SendCommentApprovalEmail::class,
        ],
        ReviewCreated::class => [
            UpdateModelRating::class,
        ],
        ReviewApproved::class => [
            UpdateModelRating::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register Review Observer
        \App\Interactions\Review::observe(\App\Observers\ReviewObserver::class);

        // Register Post Observer
        \App\Domains\Blog\Models\Post::observe(\App\Observers\PostObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

