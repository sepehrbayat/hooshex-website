<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Events\UserRegistered;
use App\Domains\Commerce\Events\OrderCreated;
use App\Domains\Commerce\Events\OrderPaid;
use App\Domains\Commerce\Listeners\SendOrderConfirmationEmail;
use App\Domains\Commerce\Listeners\SendPaymentConfirmationEmail;
use App\Domains\Courses\Events\CourseEnrolled;
use App\Domains\Courses\Listeners\SendEnrollmentConfirmationEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            // Add listeners here when needed
        ],
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
        ],
        OrderPaid::class => [
            SendPaymentConfirmationEmail::class,
        ],
        CourseEnrolled::class => [
            SendEnrollmentConfirmationEmail::class,
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

